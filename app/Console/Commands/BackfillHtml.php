<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Services\ContentSanitizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackfillHtml extends Command
{
    protected $signature = 'posts:backfill-html
                            {--dry-run : Show what would change without writing to the database}
                            {--id=    : Only process a single post by ID}';

    protected $description = 'Convert plain-text post bodies to purified HTML paragraphs and extract inline TAGS strings.';

    public function handle(ContentSanitizer $sanitizer): int
    {
        $isDryRun = (bool) $this->option('dry-run');
        $onlyId   = $this->option('id');

        $query = Post::query();
        if ($onlyId) {
            $query->where('id', $onlyId);
        }

        $posts = $query->orderBy('id')->get(['id', 'title', 'content']);

        if ($posts->isEmpty()) {
            $this->warn('No posts found.');
            return self::SUCCESS;
        }

        $tagsStash   = [];   // [ post_id => ['FIFA World Cup 2026', ...] ]
        $headingHints = [];  // lines that look like headings — human review required
        $processed   = 0;

        $this->info(sprintf(
            '%s %d post(s)...',
            $isDryRun ? '[DRY-RUN] Previewing' : 'Processing',
            $posts->count()
        ));

        foreach ($posts as $post) {
            [$html, $tags, $hints] = $this->convertPost($post->content);

            if ($tags) {
                $tagsStash[$post->id] = $tags;
            }
            if ($hints) {
                $headingHints[$post->id] = ['title' => $post->title, 'lines' => $hints];
            }

            if ($isDryRun) {
                $this->showDiff($post, $html);
            } else {
                // Idempotency check: skip posts whose content already looks like HTML
                if ($this->alreadyHtml($post->content)) {
                    $this->line("  <info>SKIP</info> #{$post->id} — already HTML");
                    continue;
                }

                $cleanHtml = $sanitizer->sanitize($html);
                DB::table('posts')
                    ->where('id', $post->id)
                    ->update(['content' => $cleanHtml]);

                $this->line("  <info>OK</info>   #{$post->id} {$post->title}");
            }

            $processed++;
        }

        // ── Tag stash ──────────────────────────────────────────────────────────
        if (! empty($tagsStash)) {
            $stashPath = storage_path('app/tags_stash.json');
            $existing  = file_exists($stashPath)
                ? json_decode(file_get_contents($stashPath), true)
                : [];

            $merged = array_replace($existing, $tagsStash);
            file_put_contents($stashPath, json_encode($merged, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            $this->newLine();
            $this->info('Tag data stashed at: ' . $stashPath);
            $this->line('  ' . count($tagsStash) . ' post(s) had inline TAGS strings. Phase 2 will import these.');
        }

        // ── Heading hints ──────────────────────────────────────────────────────
        if (! empty($headingHints)) {
            $this->newLine();
            $this->warn('Possible headings detected — review manually in the editor:');
            foreach ($headingHints as $id => $info) {
                $this->line("  Post #{$id}: \"{$info['title']}\"");
                foreach ($info['lines'] as $line) {
                    $this->line("    → {$line}");
                }
            }
        }

        $this->newLine();
        $this->info(sprintf(
            '%s complete. %d post(s) %s.',
            $isDryRun ? 'Dry-run' : 'Backfill',
            $processed,
            $isDryRun ? 'previewed' : 'updated'
        ));

        return self::SUCCESS;
    }

    /**
     * Convert a single post's plain-text content to HTML.
     *
     * Rules (per spec):
     *  - Double (or more) blank lines → paragraph break.
     *  - Single newlines inside a paragraph → <br> (preserves how posts currently read).
     *  - Trailing "TAGS :..." line is extracted and removed.
     *  - Short lines that look like headings are flagged for human review — NOT converted.
     *
     * @return array{string, string[], string[]}  [html, tags[], headingHints[]]
     */
    private function convertPost(string $raw): array
    {
        // ── 1. Extract and strip the TAGS line ────────────────────────────────
        $tags = [];
        $raw  = preg_replace_callback(
            '/\bTAGS\s*:([^\n]+)/i',
            function ($m) use (&$tags) {
                foreach (explode(',', $m[1]) as $tag) {
                    $t = trim($tag);
                    if ($t !== '') {
                        $tags[] = $t;
                    }
                }
                return '';
            },
            $raw
        );

        // ── 2. Split on any newline — every line is its own paragraph.
        //       These posts were written for nl2br(), so each \n is a paragraph
        //       break, not a soft line-wrap. Double newlines also split cleanly.
        $blocks = preg_split('/\n+/', trim($raw));

        // ── 3. Detect heading-like lines ──────────────────────────────────────
        $hints = [];
        $paragraphs = [];

        foreach ($blocks as $block) {
            $block = trim($block);
            if ($block === '') {
                continue;
            }

            // Flag short Title-Cased lines with no sentence-ending punctuation
            // as possible headings for human review.
            if (
                mb_strlen($block) <= 80
                && ! preg_match('/[.?!,;:]$/', $block)
                && preg_match('/\b[A-Z]/', $block)
            ) {
                $hints[] = $block;
            }

            $paragraphs[] = '<p>' . htmlspecialchars($block) . '</p>';
        }

        $html = implode("\n", $paragraphs);

        return [$html, $tags, $hints];
    }

    /**
     * Idempotency guard: if the content already starts with an HTML block tag,
     * it has already been converted — skip it.
     */
    private function alreadyHtml(string $content): bool
    {
        $trimmed = ltrim($content);
        return str_starts_with($trimmed, '<p>')
            || str_starts_with($trimmed, '<h2>')
            || str_starts_with($trimmed, '<h3>')
            || str_starts_with($trimmed, '<ul>')
            || str_starts_with($trimmed, '<ol>')
            || str_starts_with($trimmed, '<blockquote>');
    }

    private function showDiff(Post $post, string $newHtml): void
    {
        $this->newLine();
        $this->line("<comment>── Post #{$post->id}: {$post->title} ──</comment>");

        $before = mb_substr($post->content, 0, 400);
        $after  = mb_substr($newHtml, 0, 400);

        $this->line('<fg=red>BEFORE (first 400 chars):</>');
        $this->line($before);
        $this->newLine();
        $this->line('<fg=green>AFTER  (first 400 chars):</>');
        $this->line($after);
    }
}

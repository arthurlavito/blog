<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportTags extends Command
{
    protected $signature = 'tags:import
                            {--dry-run : Show what would be created/attached without writing to the database}';

    protected $description = 'Import tags from the Phase 1 tags_stash.json into the tags table and post_tag pivot.';

    public function handle(): int
    {
        $stashPath = storage_path('app/tags_stash.json');

        if (! file_exists($stashPath)) {
            $this->warn("No stash found at {$stashPath}. Nothing to import.");
            return self::SUCCESS;
        }

        $stash = json_decode(file_get_contents($stashPath), true);

        if (empty($stash)) {
            $this->warn('Stash is empty. Nothing to import.');
            return self::SUCCESS;
        }

        $isDryRun = (bool) $this->option('dry-run');

        $this->info(sprintf(
            '%s tags from %d post(s)...',
            $isDryRun ? '[DRY-RUN] Would import' : 'Importing',
            count($stash)
        ));

        $createdTags  = 0;
        $attachedPivot = 0;

        foreach ($stash as $postId => $tagNames) {
            $post = Post::find($postId);

            if (! $post) {
                $this->warn("  Post #{$postId} not found — skipping.");
                continue;
            }

            $tagIds = [];

            foreach ($tagNames as $name) {
                $name = trim($name);
                if ($name === '') {
                    continue;
                }

                $slug = Str::slug($name);

                if ($isDryRun) {
                    $exists = Tag::where('slug', $slug)->exists();
                    $this->line(sprintf(
                        '  Post #%s: %s tag "%s" (slug: %s)',
                        $postId,
                        $exists ? 'attach existing' : 'create + attach',
                        $name,
                        $slug
                    ));
                    continue;
                }

                $tag = Tag::firstOrCreate(['slug' => $slug], ['name' => $name]);

                if ($tag->wasRecentlyCreated) {
                    $createdTags++;
                }

                $tagIds[] = $tag->id;
            }

            if (! $isDryRun && ! empty($tagIds)) {
                $post->tags()->syncWithoutDetaching($tagIds);
                $attachedPivot += count($tagIds);
                $this->line("  <info>OK</info>   Post #{$postId} \"{$post->title}\" → " . count($tagIds) . ' tag(s)');
            }
        }

        $this->newLine();

        if ($isDryRun) {
            $this->info('Dry-run complete — no changes written.');
        } else {
            $this->info("Import complete. Tags created: {$createdTags}. Pivot rows written: {$attachedPivot}.");
        }

        return self::SUCCESS;
    }
}

<?php

namespace Tests\Feature;

use App\Console\Commands\BackfillHtml;
use App\Models\Post;
use App\Models\User;
use App\Services\ContentSanitizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class Phase1ContentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // HTMLPurifier writes a serializer cache; ensure the directory exists in tests
        @mkdir(storage_path('app/purifier'), 0755, true);
    }

    // ── ContentSanitizer / purifier ────────────────────────────────────────

    public function test_purifier_strips_script_tags(): void
    {
        $sanitizer = app(ContentSanitizer::class);
        $input     = '<p>Hello</p><script>alert("xss")</script><p>World</p>';

        $output = $sanitizer->sanitize($input);

        $this->assertStringNotContainsString('<script', $output);
        $this->assertStringContainsString('Hello', $output);
        $this->assertStringContainsString('World', $output);
    }

    public function test_purifier_strips_inline_styles(): void
    {
        $sanitizer = app(ContentSanitizer::class);
        $input     = '<p style="color:red;font-size:99px">Styled</p>';

        $output = $sanitizer->sanitize($input);

        $this->assertStringNotContainsString('style=', $output);
        $this->assertStringContainsString('Styled', $output);
    }

    public function test_purifier_preserves_allowed_tags(): void
    {
        $sanitizer = app(ContentSanitizer::class);
        $input     = '<h2>Heading</h2><p><strong>Bold</strong> and <em>italic</em></p><blockquote>Quote</blockquote>';

        $output = $sanitizer->sanitize($input);

        $this->assertStringContainsString('<h2>', $output);
        $this->assertStringContainsString('<strong>', $output);
        $this->assertStringContainsString('<em>', $output);
        $this->assertStringContainsString('<blockquote>', $output);
    }

    public function test_internal_links_keep_href_without_noopener(): void
    {
        config(['app.url' => 'https://anim24.com']);
        $sanitizer = app(ContentSanitizer::class);
        $input     = '<p><a href="https://anim24.com/posts/some-story">Read more</a></p>';

        $output = $sanitizer->sanitize($input);

        $this->assertStringContainsString('href="https://anim24.com/posts/some-story"', $output);
        $this->assertStringNotContainsString('noopener', $output);
    }

    public function test_external_links_get_noopener_and_target_blank(): void
    {
        config(['app.url' => 'https://anim24.com']);
        $sanitizer = app(ContentSanitizer::class);
        $input     = '<p><a href="https://bbc.com/news/article">BBC</a></p>';

        $output = $sanitizer->sanitize($input);

        $this->assertStringContainsString('noopener', $output);
        $this->assertStringContainsString('target="_blank"', $output);
    }

    public function test_existing_rel_attrs_on_external_links_get_noopener_appended(): void
    {
        config(['app.url' => 'https://anim24.com']);
        $sanitizer = app(ContentSanitizer::class);
        // rel="noreferrer" already present — noopener should be added alongside it
        $input  = '<p><a href="https://reuters.com" rel="noreferrer" target="_blank">Reuters</a></p>';
        $output = $sanitizer->sanitize($input);

        $this->assertStringContainsString('noopener', $output);
        $this->assertStringContainsString('noreferrer', $output);
    }

    // ── Backfill command ───────────────────────────────────────────────────

    public function test_backfill_converts_plain_text_to_paragraphs(): void
    {
        $author = User::factory()->create(['role' => 'author']);
        $post   = Post::factory()->create([
            'user_id' => $author->id,
            'content' => "First paragraph text here.\n\nSecond paragraph here.",
            'status'  => 0,
        ]);

        Artisan::call('posts:backfill-html', ['--id' => $post->id]);

        $updated = $post->fresh()->content;
        $this->assertStringContainsString('<p>', $updated);
        $this->assertStringContainsString('First paragraph', $updated);
        $this->assertStringContainsString('Second paragraph', $updated);
    }

    public function test_backfill_extracts_tags_string_from_body(): void
    {
        $author = User::factory()->create(['role' => 'author']);
        $post   = Post::factory()->create([
            'user_id' => $author->id,
            'content' => "Great article about football.\n\nTAGS :FIFA World Cup, Spain, France",
            'status'  => 0,
        ]);

        Artisan::call('posts:backfill-html', ['--id' => $post->id]);

        $updated = $post->fresh()->content;
        $this->assertStringNotContainsString('TAGS :', $updated);
        $this->assertStringNotContainsString('FIFA World Cup', $updated);

        // Tags stash must have been written
        $stash = json_decode(file_get_contents(storage_path('app/tags_stash.json')), true);
        $this->assertArrayHasKey($post->id, $stash);
        $this->assertContains('FIFA World Cup', $stash[$post->id]);
    }

    public function test_backfill_is_idempotent(): void
    {
        $author = User::factory()->create(['role' => 'author']);
        $post   = Post::factory()->create([
            'user_id' => $author->id,
            'content' => '<p>Already HTML content here.</p>',
            'status'  => 0,
        ]);

        Artisan::call('posts:backfill-html', ['--id' => $post->id]);

        $this->assertSame('<p>Already HTML content here.</p>', $post->fresh()->content);
    }

    public function test_backfill_dry_run_does_not_write_to_database(): void
    {
        $author   = User::factory()->create(['role' => 'author']);
        $original = "Plain text paragraph.\n\nSecond paragraph.";
        $post     = Post::factory()->create([
            'user_id' => $author->id,
            'content' => $original,
            'status'  => 0,
        ]);

        Artisan::call('posts:backfill-html', ['--id' => $post->id, '--dry-run' => true]);

        $this->assertSame($original, $post->fresh()->content);
    }
}

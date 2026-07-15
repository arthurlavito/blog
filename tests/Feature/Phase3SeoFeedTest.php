<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase3SeoFeedTest extends TestCase
{
    use RefreshDatabase;

    private User $author;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->author   = User::factory()->create(['role' => 'author']);
        $this->category = Category::create(['name' => 'Sports', 'slug' => 'sports']);
    }

    private function makePost(array $attrs = []): Post
    {
        return Post::factory()->create(array_merge([
            'user_id'     => $this->author->id,
            'category_id' => $this->category->id,
            'status'      => 0,
            'content'     => '<p>Test content for the post body.</p>',
        ], $attrs));
    }

    // ── Head meta tags ─────────────────────────────────────────────────────

    public function test_post_show_uses_meta_title_when_set(): void
    {
        $post = $this->makePost(['meta_title' => 'Custom SEO Title']);

        $this->get(route('posts.show', $post))
             ->assertOk()
             ->assertSee('Custom SEO Title | Anim24', false);
    }

    public function test_post_show_falls_back_to_post_title(): void
    {
        $post = $this->makePost(['title' => 'Original Title', 'meta_title' => null]);

        $this->get(route('posts.show', $post))
             ->assertOk()
             ->assertSee('Original Title | Anim24', false);
    }

    public function test_post_show_uses_meta_description_when_set(): void
    {
        $post = $this->makePost(['meta_description' => 'Custom meta description here.']);

        $this->get(route('posts.show', $post))
             ->assertOk()
             ->assertSee('Custom meta description here.', false);
    }

    public function test_post_show_uses_custom_canonical(): void
    {
        $post = $this->makePost(['canonical_url' => 'https://example.com/original']);

        $response = $this->get(route('posts.show', $post));

        $response->assertOk()
                 ->assertSee('https://example.com/original', false);
    }

    public function test_post_show_renders_noindex_meta_when_flagged(): void
    {
        $post = $this->makePost(['noindex' => true]);

        $this->get(route('posts.show', $post))
             ->assertOk()
             ->assertSee('noindex', false);
    }

    public function test_post_show_omits_robots_tag_when_noindex_false(): void
    {
        $post = $this->makePost(['noindex' => false]);

        $this->get(route('posts.show', $post))
             ->assertOk()
             ->assertDontSee('name="robots"', false);
    }

    // ── RSS feed ───────────────────────────────────────────────────────────

    public function test_rss_feed_returns_xml(): void
    {
        $this->makePost(['title' => 'RSS Test Article']);

        $response = $this->get('/feed.xml');

        // The package serves RSS as application/xml regardless of contentType config
        $response->assertOk();
        $this->assertStringContainsString('xml', $response->headers->get('Content-Type'));
    }

    public function test_rss_feed_contains_published_posts(): void
    {
        $post = $this->makePost(['title' => 'Visible in Feed']);

        $response = $this->get('/feed.xml');

        $response->assertOk()
                 ->assertSee('Visible in Feed', false);
    }

    public function test_rss_feed_excludes_unpublished_posts(): void
    {
        $this->makePost(['title' => 'Hidden Draft', 'status' => 1]);

        $response = $this->get('/feed.xml');

        $response->assertOk()
                 ->assertDontSee('Hidden Draft', false);
    }

    public function test_rss_feed_uses_meta_title_when_set(): void
    {
        $this->makePost([
            'title'      => 'Raw Title',
            'meta_title' => 'SEO Title for Feed',
        ]);

        $this->get('/feed.xml')
             ->assertOk()
             ->assertSee('SEO Title for Feed', false);
    }

    // ── Head RSS discovery link ────────────────────────────────────────────

    public function test_head_contains_rss_discovery_link(): void
    {
        $this->get(route('posts.index'))
             ->assertOk()
             ->assertSee('application/rss+xml', false);
    }
}

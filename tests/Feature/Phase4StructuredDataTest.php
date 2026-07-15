<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase4StructuredDataTest extends TestCase
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
            'content'     => '<p>Test content for the article body.</p>',
        ], $attrs));
    }

    // ── BreadcrumbList schema ──────────────────────────────────────────────

    public function test_post_show_renders_breadcrumb_schema(): void
    {
        $post = $this->makePost();

        $this->get(route('posts.show', $post))
             ->assertOk()
             ->assertSee('BreadcrumbList', false)
             ->assertSee('ListItem', false);
    }

    public function test_category_page_renders_breadcrumb_schema(): void
    {
        $this->get(route('categories.show', $this->category))
             ->assertOk()
             ->assertSee('BreadcrumbList', false)
             ->assertSee('Sports', false);
    }

    public function test_tag_page_renders_breadcrumb_schema(): void
    {
        $tag = Tag::create(['name' => 'FIFA', 'slug' => 'fifa']);

        $this->get(route('tags.show', $tag))
             ->assertOk()
             ->assertSee('BreadcrumbList', false)
             ->assertSee('FIFA', false);
    }

    // ── Visible breadcrumb nav ─────────────────────────────────────────────

    public function test_post_show_renders_visible_breadcrumb_nav(): void
    {
        $post = $this->makePost();

        $this->get(route('posts.show', $post))
             ->assertOk()
             ->assertSee('aria-label="Breadcrumb"', false)
             ->assertSee('Home', false)
             ->assertSee($this->category->name, false);
    }

    // ── NewsArticle schema ─────────────────────────────────────────────────

    public function test_post_show_renders_news_article_schema(): void
    {
        $post = $this->makePost(['title' => 'Test Article Headline']);

        $this->get(route('posts.show', $post))
             ->assertOk()
             ->assertSee('NewsArticle', false)
             ->assertSee('Test Article Headline', false);
    }

    public function test_news_article_includes_article_section(): void
    {
        $post = $this->makePost();

        $this->get(route('posts.show', $post))
             ->assertOk()
             ->assertSee('articleSection', false)
             ->assertSee('Sports', false);
    }

    public function test_news_article_includes_keywords_from_tags(): void
    {
        $tag  = Tag::create(['name' => 'FIFA', 'slug' => 'fifa']);
        $post = $this->makePost();
        $post->tags()->attach($tag);

        $this->get(route('posts.show', $post))
             ->assertOk()
             ->assertSee('keywords', false)
             ->assertSee('FIFA', false);
    }

    public function test_news_article_uses_meta_title_in_headline(): void
    {
        $post = $this->makePost([
            'title'      => 'Raw Title',
            'meta_title' => 'Custom SEO Headline',
        ]);

        $this->get(route('posts.show', $post))
             ->assertOk()
             ->assertSee('Custom SEO Headline', false);
    }

    // ── Site-level schema ──────────────────────────────────────────────────

    public function test_every_page_has_organization_schema(): void
    {
        $this->get(route('posts.index'))
             ->assertOk()
             ->assertSee('Organization', false)
             ->assertSee('WebSite', false);
    }

    public function test_website_schema_has_search_action(): void
    {
        $this->get(route('posts.index'))
             ->assertOk()
             ->assertSee('SearchAction', false)
             ->assertSee('search_term_string', false);
    }
}

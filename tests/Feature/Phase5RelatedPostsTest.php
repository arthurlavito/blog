<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Services\RelatedPostsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase5RelatedPostsTest extends TestCase
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
        ], $attrs));
    }

    // ── RelatedPostsService ────────────────────────────────────────────────

    public function test_related_posts_ranked_by_tag_overlap(): void
    {
        $tag1 = Tag::create(['name' => 'FIFA', 'slug' => 'fifa']);
        $tag2 = Tag::create(['name' => 'Spain', 'slug' => 'spain']);

        $subject    = $this->makePost(['title' => 'Subject Post']);
        $twoTags    = $this->makePost(['title' => 'Two Tag Match']);
        $oneTag     = $this->makePost(['title' => 'One Tag Match']);
        $noTags     = $this->makePost(['title' => 'No Tag Match']);

        $subject->tags()->attach([$tag1->id, $tag2->id]);
        $twoTags->tags()->attach([$tag1->id, $tag2->id]);
        $oneTag->tags()->attach([$tag1->id]);

        $subject->load('tags');
        $results = app(RelatedPostsService::class)->get($subject);

        $this->assertCount(2, $results);
        $this->assertEquals('Two Tag Match', $results->first()->title);
        $this->assertEquals('One Tag Match', $results->last()->title);
    }

    public function test_related_posts_excludes_current_post(): void
    {
        $tag  = Tag::create(['name' => 'FIFA', 'slug' => 'fifa']);
        $post = $this->makePost();
        $post->tags()->attach($tag);
        $post->load('tags');

        $results = app(RelatedPostsService::class)->get($post);

        $this->assertNotContains($post->id, $results->pluck('id')->all());
    }

    public function test_related_posts_falls_back_to_same_category(): void
    {
        $post  = $this->makePost(['title' => 'Subject']);
        $other = $this->makePost(['title' => 'Same Category Post']);
        $post->load('tags');

        $results = app(RelatedPostsService::class)->get($post);

        $this->assertTrue($results->pluck('id')->contains($other->id));
    }

    public function test_related_posts_excludes_unpublished(): void
    {
        $tag    = Tag::create(['name' => 'FIFA', 'slug' => 'fifa']);
        $post   = $this->makePost();
        $hidden = $this->makePost(['title' => 'Draft', 'status' => 1]);

        $post->tags()->attach($tag);
        $hidden->tags()->attach($tag);
        $post->load('tags');

        $results = app(RelatedPostsService::class)->get($post);

        $this->assertNotContains($hidden->id, $results->pluck('id')->all());
    }

    // ── Post show page integration ─────────────────────────────────────────

    public function test_post_show_renders_related_articles_section(): void
    {
        $tag  = Tag::create(['name' => 'FIFA', 'slug' => 'fifa']);
        $post = $this->makePost(['title' => 'Subject Post']);
        $rel  = $this->makePost(['title' => 'Related Article']);

        $post->tags()->attach($tag);
        $rel->tags()->attach($tag);

        $this->get(route('posts.show', $post))
             ->assertOk()
             ->assertSee('Related', false)
             ->assertSee('Related Article', false);
    }

    public function test_post_show_renders_prev_next_links(): void
    {
        $older  = $this->makePost(['title' => 'Older Post', 'created_at' => now()->subDays(2)]);
        $post   = $this->makePost(['title' => 'Current Post', 'created_at' => now()->subDay()]);
        $newer  = $this->makePost(['title' => 'Newer Post', 'created_at' => now()]);

        $this->get(route('posts.show', $post))
             ->assertOk()
             ->assertSee('Previous', false)
             ->assertSee('Next', false)
             ->assertSee('Older Post', false)
             ->assertSee('Newer Post', false);
    }

    public function test_post_show_renders_more_from_category(): void
    {
        $post  = $this->makePost(['title' => 'Main Post']);
        $this->makePost(['title' => 'Also in Sports']);

        $this->get(route('posts.show', $post))
             ->assertOk()
             ->assertSee('More from', false)
             ->assertSee('Sports', false)
             ->assertSee('Also in Sports', false);
    }
}

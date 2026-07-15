<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase6EditorialWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $author;
    private User $reader;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin    = User::factory()->create(['role' => 'admin']);
        $this->author   = User::factory()->create(['role' => 'author']);
        $this->reader   = User::factory()->create(['role' => 'reader']);
        $this->category = Category::create(['name' => 'Tech', 'slug' => 'tech']);
    }

    private function makePost(array $attrs = []): Post
    {
        return Post::factory()->create(array_merge([
            'user_id'     => $this->author->id,
            'category_id' => $this->category->id,
            'status'      => Post::STATUS_DRAFT,
        ], $attrs));
    }

    // ── Status constants ───────────────────────────────────────────────────

    public function test_status_constants_are_correct(): void
    {
        $this->assertSame(0, Post::STATUS_PUBLISHED);
        $this->assertSame(1, Post::STATUS_DRAFT);
        $this->assertSame(2, Post::STATUS_PENDING);
    }

    public function test_scope_draft_filters_correctly(): void
    {
        $this->makePost(['status' => Post::STATUS_DRAFT]);
        $this->makePost(['status' => Post::STATUS_PUBLISHED]);
        $this->makePost(['status' => Post::STATUS_PENDING]);

        $this->assertCount(1, Post::draft()->get());
    }

    public function test_scope_pending_filters_correctly(): void
    {
        $this->makePost(['status' => Post::STATUS_PENDING]);
        $this->makePost(['status' => Post::STATUS_PUBLISHED]);

        $this->assertCount(1, Post::pending()->get());
    }

    public function test_scope_published_excludes_drafts_and_pending(): void
    {
        $this->makePost(['status' => Post::STATUS_PUBLISHED]);
        $this->makePost(['status' => Post::STATUS_DRAFT]);
        $this->makePost(['status' => Post::STATUS_PENDING]);

        $this->assertCount(1, Post::published()->get());
    }

    // ── Author store → draft ───────────────────────────────────────────────

    public function test_author_store_saves_as_draft(): void
    {
        $this->actingAs($this->author)
            ->post(route('posts.store'), [
                'title'       => 'My Draft Post',
                'content'     => '<p>content</p>',
                'category_id' => $this->category->id,
            ])
            ->assertRedirect(route('dashboard'));

        $post = Post::where('title', 'My Draft Post')->first();
        $this->assertNotNull($post);
        $this->assertSame(Post::STATUS_DRAFT, $post->status);
    }

    // ── Admin store → published ────────────────────────────────────────────

    public function test_admin_store_publishes_immediately(): void
    {
        $this->actingAs($this->admin)
            ->post(route('posts.store'), [
                'title'       => 'Admin Post',
                'content'     => '<p>content</p>',
                'category_id' => $this->category->id,
            ])
            ->assertRedirect(route('dashboard'));

        $post = Post::where('title', 'Admin Post')->first();
        $this->assertNotNull($post);
        $this->assertSame(Post::STATUS_PUBLISHED, $post->status);
    }

    // ── Submit for review ──────────────────────────────────────────────────

    public function test_author_can_submit_draft_for_review(): void
    {
        $post = $this->makePost(['status' => Post::STATUS_DRAFT]);

        $this->actingAs($this->author)
            ->post(route('posts.submit', $post))
            ->assertRedirect();

        $this->assertSame(Post::STATUS_PENDING, $post->fresh()->status);
    }

    public function test_author_cannot_submit_already_published_post(): void
    {
        $post = $this->makePost(['status' => Post::STATUS_PUBLISHED]);

        $this->actingAs($this->author)
            ->post(route('posts.submit', $post))
            ->assertForbidden();
    }

    public function test_reader_cannot_submit_for_review(): void
    {
        $post = $this->makePost(['status' => Post::STATUS_DRAFT]);

        $this->actingAs($this->reader)
            ->post(route('posts.submit', $post))
            ->assertForbidden();
    }

    // ── Admin publish ──────────────────────────────────────────────────────

    public function test_admin_can_publish_pending_post(): void
    {
        $post = $this->makePost(['status' => Post::STATUS_PENDING]);

        $this->actingAs($this->admin)
            ->post(route('admin.posts.publish', $post))
            ->assertRedirect();

        $this->assertSame(Post::STATUS_PUBLISHED, $post->fresh()->status);
    }

    public function test_author_cannot_publish_post(): void
    {
        $post = $this->makePost(['status' => Post::STATUS_PENDING]);

        $this->actingAs($this->author)
            ->post(route('admin.posts.publish', $post))
            ->assertForbidden();
    }

    // ── Admin reject ───────────────────────────────────────────────────────

    public function test_admin_can_reject_pending_post(): void
    {
        $post = $this->makePost(['status' => Post::STATUS_PENDING]);

        $this->actingAs($this->admin)
            ->post(route('admin.posts.reject', $post))
            ->assertRedirect();

        $this->assertSame(Post::STATUS_DRAFT, $post->fresh()->status);
    }

    public function test_author_cannot_reject_post(): void
    {
        $post = $this->makePost(['status' => Post::STATUS_PENDING]);

        $this->actingAs($this->author)
            ->post(route('admin.posts.reject', $post))
            ->assertForbidden();
    }

    // ── ActivityLogger ─────────────────────────────────────────────────────

    public function test_activity_logger_records_entry(): void
    {
        $post = $this->makePost();

        app(ActivityLogger::class)->log('test_action', $post, 'test desc', $this->admin->id);

        $this->assertDatabaseHas('activity_log', [
            'action'       => 'test_action',
            'subject_type' => Post::class,
            'subject_id'   => $post->id,
            'user_id'      => $this->admin->id,
        ]);
    }

    // ── Author profile ─────────────────────────────────────────────────────

    public function test_author_profile_page_renders(): void
    {
        $post = $this->makePost(['status' => Post::STATUS_PUBLISHED]);

        $this->get(route('authors.show', $this->author))
            ->assertOk()
            ->assertSee($this->author->name)
            ->assertSee($post->title);
    }

    public function test_author_profile_hides_drafts(): void
    {
        $draft = $this->makePost(['title' => 'Secret Draft', 'status' => Post::STATUS_DRAFT]);

        $this->get(route('authors.show', $this->author))
            ->assertOk()
            ->assertDontSee('Secret Draft');
    }

    public function test_author_profile_shows_bio(): void
    {
        $this->author->update(['bio' => 'Sports journalist and anime fan.']);

        $this->get(route('authors.show', $this->author))
            ->assertOk()
            ->assertSee('Sports journalist and anime fan.');
    }

    // ── Admin dashboard pending queue ──────────────────────────────────────

    public function test_admin_dashboard_shows_pending_queue(): void
    {
        $pending = $this->makePost(['title' => 'Awaiting Approval', 'status' => Post::STATUS_PENDING]);

        $this->actingAs($this->admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Awaiting Approval');
    }
}

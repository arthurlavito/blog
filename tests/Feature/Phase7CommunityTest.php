<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase7CommunityTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $author;
    private User $reader;
    private Post $post;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin  = User::factory()->create(['role' => 'admin']);
        $this->author = User::factory()->create(['role' => 'author']);
        $this->reader = User::factory()->create(['role' => 'reader']);

        $cat = Category::create(['name' => 'Tech', 'slug' => 'tech']);
        $this->post = Post::factory()->create([
            'user_id'     => $this->author->id,
            'category_id' => $cat->id,
            'status'      => Post::STATUS_PUBLISHED,
        ]);
    }

    // ── Authentication gate ────────────────────────────────────────────────

    public function test_unauthenticated_user_cannot_comment(): void
    {
        $this->post(route('comments.store', $this->post), ['body' => 'Hello'])
            ->assertRedirect(route('login'));
    }

    public function test_unauthenticated_user_cannot_react(): void
    {
        $response = $this->postJson(route('posts.like'), [
            'id'       => $this->post->id,
            'type'     => 'post',
            'reaction' => 'like',
        ]);
        $response->assertStatus(401);
    }

    // ── First comment lands pending ────────────────────────────────────────

    public function test_first_comment_is_pending(): void
    {
        $this->actingAs($this->reader)
            ->post(route('comments.store', $this->post), ['body' => 'My first comment'])
            ->assertRedirect();

        $comment = Comment::where('user_id', $this->reader->id)->first();
        $this->assertNotNull($comment);
        $this->assertSame(Comment::STATUS_PENDING, $comment->status);
    }

    // ── Auto-approve for users with approved history ───────────────────────

    public function test_user_with_approved_history_auto_approves(): void
    {
        // Pre-seed one approved comment for this user
        Comment::factory()->create([
            'user_id' => $this->reader->id,
            'post_id' => $this->post->id,
            'status'  => Comment::STATUS_APPROVED,
        ]);

        $this->actingAs($this->reader)
            ->post(route('comments.store', $this->post), ['body' => 'Second comment'])
            ->assertRedirect();

        $latest = Comment::where('user_id', $this->reader->id)
            ->where('body', 'Second comment')
            ->first();

        $this->assertSame(Comment::STATUS_APPROVED, $latest->status);
    }

    // ── Honeypot ──────────────────────────────────────────────────────────

    public function test_honeypot_silently_discards_bot_comment(): void
    {
        $initialCount = Comment::count();

        $this->actingAs($this->reader)
            ->post(route('comments.store', $this->post), [
                'body'    => 'Buy cheap pills',
                'website' => 'http://spam.example.com',
            ])
            ->assertRedirect();

        $this->assertSame($initialCount, Comment::count());
    }

    // ── Admin moderation ───────────────────────────────────────────────────

    public function test_admin_can_approve_comment(): void
    {
        $comment = Comment::factory()->create([
            'user_id' => $this->reader->id,
            'post_id' => $this->post->id,
            'status'  => Comment::STATUS_PENDING,
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.comments.approve', $comment))
            ->assertRedirect();

        $this->assertSame(Comment::STATUS_APPROVED, $comment->fresh()->status);
    }

    public function test_admin_can_mark_comment_as_spam(): void
    {
        $comment = Comment::factory()->create([
            'user_id' => $this->reader->id,
            'post_id' => $this->post->id,
            'status'  => Comment::STATUS_PENDING,
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.comments.spam', $comment))
            ->assertRedirect();

        $this->assertSame(Comment::STATUS_SPAM, $comment->fresh()->status);
    }

    public function test_admin_can_trash_comment(): void
    {
        $comment = Comment::factory()->create([
            'user_id' => $this->reader->id,
            'post_id' => $this->post->id,
            'status'  => Comment::STATUS_PENDING,
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.comments.trash', $comment))
            ->assertRedirect();

        $this->assertSame(Comment::STATUS_TRASHED, $comment->fresh()->status);
    }

    public function test_non_admin_cannot_approve_comment(): void
    {
        $comment = Comment::factory()->create([
            'user_id' => $this->reader->id,
            'post_id' => $this->post->id,
            'status'  => Comment::STATUS_PENDING,
        ]);

        $this->actingAs($this->author)
            ->post(route('admin.comments.approve', $comment))
            ->assertForbidden();
    }

    // ── Pending comments never render publicly ─────────────────────────────

    public function test_pending_comment_does_not_render_on_post_page(): void
    {
        Comment::factory()->create([
            'user_id' => $this->reader->id,
            'post_id' => $this->post->id,
            'body'    => 'Secret pending message',
            'status'  => Comment::STATUS_PENDING,
        ]);

        $this->get(route('posts.show', $this->post))
            ->assertOk()
            ->assertDontSee('Secret pending message');
    }

    public function test_spam_comment_does_not_render_on_post_page(): void
    {
        Comment::factory()->create([
            'user_id' => $this->reader->id,
            'post_id' => $this->post->id,
            'body'    => 'Buy cheap pills',
            'status'  => Comment::STATUS_SPAM,
        ]);

        $this->get(route('posts.show', $this->post))
            ->assertOk()
            ->assertDontSee('Buy cheap pills');
    }

    public function test_approved_comment_renders_on_post_page(): void
    {
        Comment::factory()->create([
            'user_id' => $this->reader->id,
            'post_id' => $this->post->id,
            'body'    => 'This is visible',
            'status'  => Comment::STATUS_APPROVED,
        ]);

        $this->get(route('posts.show', $this->post))
            ->assertOk()
            ->assertSee('This is visible');
    }

    // ── Comment length validation ──────────────────────────────────────────

    public function test_comment_body_must_be_at_least_2_chars(): void
    {
        $this->actingAs($this->reader)
            ->post(route('comments.store', $this->post), ['body' => 'X'])
            ->assertSessionHasErrors('body');
    }

    public function test_comment_body_cannot_exceed_2000_chars(): void
    {
        $this->actingAs($this->reader)
            ->post(route('comments.store', $this->post), ['body' => str_repeat('a', 2001)])
            ->assertSessionHasErrors('body');
    }

    // ── Reactions ─────────────────────────────────────────────────────────

    public function test_user_can_react_to_post(): void
    {
        $this->actingAs($this->reader)
            ->postJson(route('posts.like'), [
                'id'       => $this->post->id,
                'type'     => 'post',
                'reaction' => 'fire',
            ])
            ->assertOk()
            ->assertJsonPath('status', 'reacted')
            ->assertJsonPath('user_reaction', 'fire');

        $this->assertDatabaseHas('likes', [
            'user_id'       => $this->reader->id,
            'likeable_type' => Post::class,
            'likeable_id'   => $this->post->id,
            'type'          => 'fire',
        ]);
    }

    public function test_tapping_same_reaction_removes_it(): void
    {
        Like::create([
            'user_id'       => $this->reader->id,
            'likeable_type' => Post::class,
            'likeable_id'   => $this->post->id,
            'type'          => 'like',
        ]);

        $this->actingAs($this->reader)
            ->postJson(route('posts.like'), [
                'id'       => $this->post->id,
                'type'     => 'post',
                'reaction' => 'like',
            ])
            ->assertOk()
            ->assertJsonPath('status', 'removed')
            ->assertJsonPath('user_reaction', null);

        $this->assertDatabaseMissing('likes', [
            'user_id'       => $this->reader->id,
            'likeable_type' => Post::class,
            'likeable_id'   => $this->post->id,
        ]);
    }

    public function test_switching_reaction_updates_type(): void
    {
        Like::create([
            'user_id'       => $this->reader->id,
            'likeable_type' => Post::class,
            'likeable_id'   => $this->post->id,
            'type'          => 'like',
        ]);

        $this->actingAs($this->reader)
            ->postJson(route('posts.like'), [
                'id'       => $this->post->id,
                'type'     => 'post',
                'reaction' => 'fire',
            ])
            ->assertOk()
            ->assertJsonPath('status', 'switched')
            ->assertJsonPath('user_reaction', 'fire');

        $this->assertDatabaseHas('likes', [
            'user_id'       => $this->reader->id,
            'likeable_type' => Post::class,
            'likeable_id'   => $this->post->id,
            'type'          => 'fire',
        ]);
    }

    public function test_one_reaction_per_user_per_post(): void
    {
        $this->actingAs($this->reader)
            ->postJson(route('posts.like'), ['id' => $this->post->id, 'type' => 'post', 'reaction' => 'like']);

        $this->actingAs($this->reader)
            ->postJson(route('posts.like'), ['id' => $this->post->id, 'type' => 'post', 'reaction' => 'love']);

        $this->assertSame(1, Like::where('user_id', $this->reader->id)
            ->where('likeable_type', Post::class)
            ->where('likeable_id', $this->post->id)
            ->count());
    }

    // ── Admin dashboard shows pending queue ────────────────────────────────

    public function test_admin_dashboard_shows_pending_comment_queue(): void
    {
        Comment::factory()->create([
            'user_id' => $this->reader->id,
            'post_id' => $this->post->id,
            'body'    => 'Please approve me',
            'status'  => Comment::STATUS_PENDING,
        ]);

        $this->actingAs($this->admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Please approve me');
    }
}

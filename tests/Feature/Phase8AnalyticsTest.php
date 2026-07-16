<?php

namespace Tests\Feature;

use App\Console\Commands\FlushPostViews;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class Phase8AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    private Post $post;

    protected function setUp(): void
    {
        parent::setUp();
        $author   = User::factory()->create(['role' => 'author']);
        $category = Category::create(['name' => 'Tech', 'slug' => 'tech']);
        $this->post = Post::factory()->create([
            'user_id'     => $author->id,
            'category_id' => $category->id,
            'status'      => Post::STATUS_PUBLISHED,
            'views'       => 0,
        ]);
    }

    // ── View buffering ─────────────────────────────────────────────────────

    public function test_guest_view_adds_to_cache_buffer(): void
    {
        Cache::flush();

        $this->get(route('posts.show', $this->post))->assertOk();

        $buffer = Cache::get('post_views_buffer', []);
        $this->assertArrayHasKey($this->post->id, $buffer);
        $this->assertSame(1, $buffer[$this->post->id]);
    }

    public function test_authenticated_view_does_not_add_to_buffer(): void
    {
        Cache::flush();
        $user = User::factory()->create(['role' => 'reader']);

        $this->actingAs($user)->get(route('posts.show', $this->post))->assertOk();

        $buffer = Cache::get('post_views_buffer', []);
        $this->assertEmpty($buffer);
    }

    public function test_buffer_accumulates_multiple_views(): void
    {
        Cache::flush();

        $this->get(route('posts.show', $this->post));
        $this->get(route('posts.show', $this->post));
        $this->get(route('posts.show', $this->post));

        $buffer = Cache::get('post_views_buffer', []);
        $this->assertSame(3, $buffer[$this->post->id]);
    }

    // ── FlushPostViews command ─────────────────────────────────────────────

    public function test_flush_command_writes_buffer_to_database(): void
    {
        Cache::put('post_views_buffer', [$this->post->id => 42]);

        $this->artisan('posts:flush-views')->assertExitCode(0);

        $this->assertSame(42, $this->post->fresh()->views);
        $this->assertEmpty(Cache::get('post_views_buffer', []));
    }

    public function test_flush_command_is_additive(): void
    {
        $this->post->update(['views' => 100]);
        Cache::put('post_views_buffer', [$this->post->id => 10]);

        $this->artisan('posts:flush-views')->assertExitCode(0);

        $this->assertSame(110, $this->post->fresh()->views);
    }

    public function test_flush_command_with_empty_buffer_is_noop(): void
    {
        Cache::forget('post_views_buffer');

        $this->artisan('posts:flush-views')->assertExitCode(0);

        $this->assertSame(0, $this->post->fresh()->views);
    }

    // ── GA4 snippet — production only ─────────────────────────────────────

    public function test_ga4_snippet_absent_when_no_key_configured(): void
    {
        config(['services.google.analytics_id' => null]);

        $this->get(route('posts.show', $this->post))
            ->assertOk()
            ->assertDontSee('googletagmanager.com', false);
    }

    // ── Post show page contains event hooks ───────────────────────────────

    public function test_post_show_contains_share_network_attributes(): void
    {
        $this->get(route('posts.show', $this->post))
            ->assertOk()
            ->assertSee('data-share-network="x"', false)
            ->assertSee('data-share-network="whatsapp"', false)
            ->assertSee('data-share-network="copy"', false);
    }

    public function test_post_show_contains_ga4_event_script(): void
    {
        $this->get(route('posts.show', $this->post))
            ->assertOk()
            ->assertSee('post_read', false)
            ->assertSee('share_click', false)
            ->assertSee('related_click', false);
    }

    // ── Admin post list shows view count ──────────────────────────────────

    public function test_admin_post_list_shows_views(): void
    {
        $this->post->update(['views' => 1234]);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('1,234');
    }
}

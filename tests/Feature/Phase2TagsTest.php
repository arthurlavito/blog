<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Phase2TagsTest extends TestCase
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

    // ── Category archive ───────────────────────────────────────────────────

    public function test_category_page_lists_published_posts(): void
    {
        Post::factory()->count(3)->create([
            'user_id'     => $this->author->id,
            'category_id' => $this->category->id,
            'status'      => 0,
        ]);

        $response = $this->get(route('categories.show', $this->category));

        $response->assertOk();
        $response->assertSee($this->category->name);
    }

    public function test_category_page_excludes_unpublished_posts(): void
    {
        $hidden = Post::factory()->create([
            'user_id'     => $this->author->id,
            'category_id' => $this->category->id,
            'status'      => 1,
            'title'       => 'Draft Post Should Be Hidden',
        ]);

        $response = $this->get(route('categories.show', $this->category));

        $response->assertOk();
        $response->assertDontSee('Draft Post Should Be Hidden');
    }

    // ── Legacy ?category= redirect ─────────────────────────────────────────

    public function test_legacy_category_query_string_redirects_to_clean_url(): void
    {
        $response = $this->get(route('posts.index', ['category' => $this->category->id]));

        $response->assertRedirect(route('categories.show', $this->category));
        $response->assertStatus(301);
    }

    public function test_unknown_category_id_does_not_redirect(): void
    {
        // If the ID doesn't resolve, index just loads normally
        $response = $this->get(route('posts.index', ['category' => 9999]));

        $response->assertOk();
    }

    // ── Tag archive ────────────────────────────────────────────────────────

    public function test_tag_page_lists_posts_with_that_tag(): void
    {
        $tag  = Tag::create(['name' => 'FIFA', 'slug' => 'fifa']);
        $post = Post::factory()->create([
            'user_id' => $this->author->id,
            'status'  => 0,
            'title'   => 'World Cup 2026 Preview',
        ]);
        $post->tags()->attach($tag);

        $response = $this->get(route('tags.show', $tag));

        $response->assertOk();
        $response->assertSee('World Cup 2026 Preview');
    }

    public function test_tag_page_excludes_posts_without_that_tag(): void
    {
        $tag  = Tag::create(['name' => 'Anime', 'slug' => 'anime']);
        Post::factory()->create([
            'user_id' => $this->author->id,
            'status'  => 0,
            'title'   => 'Untagged Article',
        ]);

        $response = $this->get(route('tags.show', $tag));

        $response->assertOk();
        $response->assertDontSee('Untagged Article');
    }

    // ── tags:import command ────────────────────────────────────────────────

    public function test_import_creates_tags_and_attaches_to_posts(): void
    {
        $post = Post::factory()->create([
            'user_id' => $this->author->id,
            'status'  => 0,
        ]);

        $stash = [$post->id => ['FIFA World Cup', 'Spain']];
        Storage::fake('local');
        file_put_contents(storage_path('app/tags_stash.json'), json_encode($stash));

        Artisan::call('tags:import');

        $this->assertDatabaseHas('tags', ['slug' => 'fifa-world-cup']);
        $this->assertDatabaseHas('tags', ['slug' => 'spain']);
        $this->assertCount(2, $post->fresh()->tags);
    }

    public function test_import_is_idempotent(): void
    {
        $post = Post::factory()->create([
            'user_id' => $this->author->id,
            'status'  => 0,
        ]);

        $stash = [$post->id => ['FIFA World Cup']];
        file_put_contents(storage_path('app/tags_stash.json'), json_encode($stash));

        Artisan::call('tags:import');
        Artisan::call('tags:import');

        $this->assertCount(1, $post->fresh()->tags);
        $this->assertDatabaseCount('tags', 1);
    }

    public function test_import_dry_run_writes_nothing(): void
    {
        $post = Post::factory()->create([
            'user_id' => $this->author->id,
            'status'  => 0,
        ]);

        $stash = [$post->id => ['Solo Leveling']];
        file_put_contents(storage_path('app/tags_stash.json'), json_encode($stash));

        Artisan::call('tags:import', ['--dry-run' => true]);

        $this->assertDatabaseMissing('tags', ['slug' => 'solo-leveling']);
        $this->assertCount(0, $post->fresh()->tags);
    }
}

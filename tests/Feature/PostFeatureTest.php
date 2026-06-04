<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostFeatureTest extends TestCase
{
    use RefreshDatabase; // This is the "Magic Eraser"

    /** @test */
    public function an_admin_can_feature_a_post()
    {
        // 1. ARRANGE: Create an Admin and a Post
        $admin = User::factory()->create(['role' => 'admin']);
        $post = Post::factory()->create(['is_featured' => false]);

        // 2. ACT: Log in as Admin and click the 'Feature' button
        $response = $this->actingAs($admin)
                         ->post(route('admin.posts.feature', $post));

        // 3. ASSERT: Check if it worked
        $this->assertTrue($post->fresh()->is_featured);
        $response->assertRedirect();
    }
}
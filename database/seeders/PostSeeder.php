<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use App\Models\Comment;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $users = User::all();
        $categories = Category::all();

        if ($categories->isEmpty() || $users->isEmpty()) {
            $this->command->error("Seed users and categories first!");
            return;
        }

        // 1. Create the Featured Post
        $featured = Post::factory()->create([
            'title'       => 'The Future of Animation: Why Solo Leveling Changed Everything',
            'user_id'     => $admin->id,
            'category_id' => $categories->where('name', 'Reviews')->first()->id ?? $categories->random()->id,
            'is_featured' => true,
        ]);
        $this->addInteractions($featured, $users);

        // 2. Create 20 Regular Posts
        Post::factory(20)->create([
            'user_id'     => $admin->id,
            'category_id' => fn() => $categories->random()->id,
            'is_featured' => false,
        ])->each(function ($post) use ($users) {
            $this->addInteractions($post, $users);
        });
    }

    /**
     * Helper function to add comments and likes to a post
     */
    private function addInteractions($post, $users)
    {
        // Add 3-7 random comments per post
        Comment::factory(rand(3, 7))->create([
            'post_id' => $post->id,
            'user_id' => $users->random()->id,
        ]);

        // Add likes (hypes) from a random selection of users
        $likers = $users->random(min(rand(5, 10), $users->count()));
        foreach ($likers as $user) {
            $post->likes()->create(['user_id' => $user->id]);
        }
    }
}
<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->error("Seed users first!");
            return;
        }

        Post::factory(20)->create()->each(function ($post) use ($users) {
            // Create 5 comments per post
            Comment::factory(5)->create([
                'post_id' => $post->id,
                'user_id' => $users->random()->id,
            ]);

            // Add hypes from a random selection of users
            $likers = $users->random(min(5, $users->count()));
            foreach ($likers as $user) {
                $post->likes()->create(['user_id' => $user->id]);
            }
        });
    }
}
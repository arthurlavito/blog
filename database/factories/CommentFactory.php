<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'body' => fake()->paragraph(1),
            // These will be overwritten by your Seeder, but good to have as defaults
            'user_id' => User::factory(),
            'post_id' => Post::factory(),
        ];
    }
}
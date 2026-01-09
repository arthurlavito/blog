<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->sentence(8);
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => fake()->paragraphs(4, true),
            'category' => fake()->randomElement(['Anime News', 'Manga', 'Reviews', 'Culture']),
            'user_id' => User::whereIn('role', ['admin', 'author'])->inRandomOrder()->first()?->id ?? User::factory(),
            'image' => null,
            'created_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
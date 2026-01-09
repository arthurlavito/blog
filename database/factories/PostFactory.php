<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(8);

        return [
            'title'      => $title,
            'slug'       => Str::slug($title),
            'content'    => fake()->paragraphs(4, true),
            'image'      => null,
            'views'      => fake()->numberBetween(0, 500),
            'created_at' => fake()->dateTimeBetween('-1 month', 'now'),
            
            // We REMOVED 'category' and 'user_id' from here.
            // These are now handled dynamically in the PostSeeder 
            // to ensure they match real Categories and Users.
        ];
    }
}
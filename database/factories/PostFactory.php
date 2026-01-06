<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    public function definition(): array
    {
        $title = $this->faker->sentence();
        
        return [
            // This creates posts the "Senior" way: it picks a random existing user ID
            'user_id' => User::pluck('id')->random(), 
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => $this->faker->paragraphs(3, true),
            'category' => $this->faker->randomElement(['Tech', 'Lifestyle', 'News', 'Gaming']),
            'image' => null, // Better to leave null than to have broken image paths
            'views' => $this->faker->numberBetween(0, 500),
        ];
    }
}
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    protected $model = \App\Models\Post::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(6, true);

        return [
            'title' => $title,
            'slug'     => Str::slug($title) . '-' . Str::random(5), // unique-ish slug
            'category' => $this->faker->randomElement(['Tech', 'Health', 'Sports', 'Politics', 'Lifestyle']),
            'content' => $this->faker->paragraphs(3, true),
            'image' => $this->faker->image('storage/app/public/images', 640, 480, null, false),

        ];
    }
}

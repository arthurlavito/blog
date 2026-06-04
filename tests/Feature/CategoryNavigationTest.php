<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_header_renders_category_names_and_filter_links(): void
    {
        $category = Category::create([
            'name' => 'Manga',
            'slug' => 'manga',
        ]);

        $this->get(route('posts.index'))
            ->assertOk()
            ->assertSeeText('Manga')
            ->assertSee(route('posts.index', ['category' => $category->id]), false);
    }
}

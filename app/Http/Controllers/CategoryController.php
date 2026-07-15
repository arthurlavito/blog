<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function show(Category $category)
    {
        $posts = Post::with(['user', 'category', 'likes'])
            ->published()
            ->where('category_id', $category->id)
            ->latest()
            ->paginate(6)
            ->withQueryString();

        $categories = Cache::remember('nav_categories', 3600, fn () =>
            Category::orderBy('name')->get()
        );

        $latestPosts = Post::published()->latest()->take(5)->get();

        $activeCategory = $category;

        return view('categories.show', compact('category', 'posts', 'categories', 'latestPosts', 'activeCategory'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories,name|max:50',
        ]);

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        Cache::forget('nav_categories');

        return back()->with('success', 'Category created successfully!');
    }
}
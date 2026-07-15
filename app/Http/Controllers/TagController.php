<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Support\Facades\Cache;

class TagController extends Controller
{
    public function show(Tag $tag)
    {
        $posts = Post::with(['user', 'category', 'likes'])
            ->published()
            ->whereHas('tags', fn ($q) => $q->where('tags.id', $tag->id))
            ->latest()
            ->paginate(6)
            ->withQueryString();

        $categories = Cache::remember('nav_categories', 3600, fn () =>
            Category::orderBy('name')->get()
        );

        $latestPosts = Post::published()->latest()->take(5)->get();

        return view('tags.show', compact('tag', 'posts', 'categories', 'latestPosts'));
    }
}

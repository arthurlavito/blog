<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use App\Models\Category;

class AuthorController extends Controller
{
    public function show(User $user)
    {
        $posts = Post::with(['category'])
            ->published()
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(9);

        $categories = Cache::remember('nav_categories', 3600, fn () =>
            Category::orderBy('name')->get()
        );

        return view('authors.show', compact('user', 'posts', 'categories'));
    }
}

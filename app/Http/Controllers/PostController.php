<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PostController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of posts with Search and Category filtering.
     */
    public function index(Request $request)
    {
        // Eager-load 'user' AND 'likes' to prevent N+1 queries
        $posts = Post::with(['user', 'likes']) 
            ->latest()
            ->filter($request->only(['search', 'category'])) 
            ->paginate(6)
            ->withQueryString();

        $categories = Post::select('category')
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->get();

        $latestPosts = Post::latest()->take(5)->get();

        return view('posts.index', compact('posts', 'categories', 'latestPosts'));
    }

    /**
     * Show the form for creating a new post.
     * Fixed: Ensure this is accessible and not confused with a slug
     */
    public function create()
    {
        // This automatically checks PostPolicy@create
        // It handles the Admin check via your 'before' method 
        // and the Author check via the 'create' method.
        $this->authorize('create', Post::class);

        return view('posts.create');
    }
    public function store(Request $request)
    {
        // 1. Authorize using Policy
        $this->authorize('create', Post::class);

        // 2. Validate
        $validated = $request->validate([
            'title'    => 'required|string|max:255',
            'content'  => 'required|string',
            'category' => 'nullable|string|max:100',
            'image'    => 'nullable|image|max:2048', 
        ]);

        // 3. Prepare Data
        $validated['slug'] = Str::slug($validated['title']);
        $validated['user_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('posts', 'public');
        }

        // 4. Create Post
        Post::create($validated);

        return redirect()->route('dashboard')->with('success', 'Post created successfully!');
    }

    /**
     * Display the specified post.
     * Performance: Eager load comments, comment users, and comment likes
     */
    public function show(Post $post)
    {
        // If your show route uses {post:slug}, this works perfectly.
        // We load everything in one go to make the page load instantly.
        $post->load([
            'user', 
            'likes', 
            'comments.user', 
            'comments.likes'
        ]);

        $post->increment('views');

        $latestPosts = Post::latest()->take(5)->get();
        $categories = Post::select('category')->whereNotNull('category')->distinct()->get();

        return view('posts.show', compact('post', 'latestPosts', 'categories'));
    }

    public function edit(Post $post)
    {
        $this->authorize('update', $post);
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title'    => 'required|string|max:255',
            'content'  => 'required|string',
            'category' => 'required|string|max:100',
            'image'    => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $validated['image'] = $request->file('image')->store('posts', 'public');
        }

        if ($post->title !== $validated['title']) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $post->update($validated);

        return redirect()->route('posts.index')->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post deleted permanently.');
    }
}
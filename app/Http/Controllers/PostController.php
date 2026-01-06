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
        // Senior Tip: Use 'with' to eager-load users and prevent N+1 performance issues
        $posts = Post::with('user')
            ->latest()
            ->filter($request->only(['search', 'category'])) // Uses the scope from Post model
            ->paginate(6)
            ->withQueryString();

        // Optimized: Fetch unique categories efficiently
        $categories = Post::select('category')
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->get();

        $latestPosts = Post::latest()->take(5)->get();

        return view('posts.index', compact('posts', 'categories', 'latestPosts'));
    }

    public function create()
    {
        $this->authorize('create', Post::class);
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Post::class);

        $validated = $request->validate([
            'title'    => 'required|string|max:255',
            'content'  => 'required|string',
            'category' => 'nullable|string|max:100',
            'image'    => 'nullable|image|max:2048',
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        $validated['user_id'] = $request->user()->id;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('posts', 'public');
        }

        Post::create($validated);

        return redirect()->route('posts.index')->with('success', 'Post created successfully.');
    }

    public function show(Post $post)
    {
        $post->increment('views');

        $latestPosts = Post::latest()->take(5)->get();
        
        // Use the categories we already have in memory or a quick query
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
            // Cleanup: Delete old image if it exists
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $validated['image'] = $request->file('image')->store('posts', 'public');
        }

        // Only regenerate slug if title changes
        if ($post->title !== $validated['title']) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $post->update($validated);

        return redirect()->route('posts.index')->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        // Cleanup: Remove file from physical storage
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post deleted permanently.');
    }
}
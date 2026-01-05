<?php

namespace App\Http\Controllers;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
        {
            $query = Post::latest();

            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            }

            if ($request->has('category')) {
                $query->where('category', $request->category);
            }

            $posts = $query->paginate(10);

            // Sidebar data
            $latestPosts = Post::latest()->take(5)->get();
            $categories = Post::select('category')->distinct()->get();

            return view('posts.index', compact('posts', 'latestPosts', 'categories'));
        }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'category' => 'required|max:100',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
        $validated['image'] = $request->file('image')->store('images', 'public');

        }

        // Auto-generate slug from title
        $validated['slug'] = Str::slug($validated['title'], '-');

        Post::create($validated);

        return redirect()->route('posts.index')->with('success', 'Post created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::findOrFail($id);

        // Increment views safely
        $post->increment('views');
        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $post = Post::findOrFail($id);
        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $post = Post::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'category' => 'required|max:100',
            'image' => 'nullable|image|max:2048',
        ]);

        //handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $validated['image'] = $request->file('image')->store('images', 'public');
        }
         // Optionally update slug if title changed
        if ($post->title !== $validated['title']) {
            $validated['slug'] = Str::slug($validated['title'], '-');
        }

        $post->update($validated);

        return redirect()->route('posts.index')->with('success', 'Post updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::findOrFail($id);
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
    }

        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post deleted successfully.');
    }
}

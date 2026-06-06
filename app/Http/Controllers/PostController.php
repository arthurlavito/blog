<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Http\RedirectResponse;

class PostController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of posts with Search and Category filtering.
     */
    public function index(Request $request)
    {
        // 1. Get the latest featured post (Eager load category)
        $featuredPost = Post::with('user', 'category')
            ->where('is_featured', true)
            ->latest()
            ->first();

        // 2. Get the paginated posts (excluding featured)
        $posts = Post::with(['user', 'category', 'likes']) 
            ->when($featuredPost, function ($query) use ($featuredPost) {
                return $query->where('id', '!=', $featuredPost->id);
            })
            ->latest()
            ->filter($request->only(['search', 'category'])) 
            ->paginate(6)
            ->withQueryString();

        // 3. Fetch from Category Model (Fixes the SQL error)
        $categories = Category::orderBy('name')->get();

        $latestPosts = Post::latest()->take(5)->get();

        return view('posts.index', compact('posts', 'featuredPost', 'categories', 'latestPosts'));
    }

    /**
     * Show the form for creating a new post.
     */
    public function create()
    {
        $this->authorize('create', Post::class);
        
        // Need to pass categories to the create form dropdown
        $categories = Category::orderBy('name')->get();

        return view('posts.create', compact('categories'));
    }

    /**
     * Store a newly created post in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Post::class);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'content'     => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $validated['user_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '.webp';
            $path = 'posts/' . $filename;

            $image = Image::read($file)
                ->scale(width: 1200) 
                ->toWebp(80);

            Storage::disk('public')->put($path, (string) $image);
            $validated['image'] = $path;
        }

        Post::create($validated);

        return redirect()->route('dashboard')->with('success', 'Post published!');
    }

    /**
     * Display the specified post.
     */
    public function show(Post $post)
    {
        $post->load([
            'user', 
            'category',
            'likes', 
            'comments.user', 
            'comments.likes'
        ]);

        $post->increment('views');

        $latestPosts = Post::latest()->take(5)->get();
        $categories = Category::orderBy('name')->get();

        return view('posts.show', compact('post', 'latestPosts', 'categories'));
    }

    /**
     * Show the form for editing.
     */
    public function edit(Post $post)
    {
        $this->authorize('update', $post);
        $categories = Category::orderBy('name')->get();
        
        return view('posts.edit', compact('post', 'categories'));
    }

    /**
     * Update the post in storage.
     */
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'content'     => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        if ($request->hasFile('image')) {
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }

            $file = $request->file('image');
            $filename = time() . '.webp';
            $path = 'posts/' . $filename;

            $image = Image::read($file)
                ->scale(width: 1200) 
                ->toWebp(80);

            Storage::disk('public')->put($path, (string) $image);
            $validated['image'] = $path;
        }

        $post->update($validated);

        return redirect()->route('posts.index')->with('success', 'Post updated.');
    }

    /**
     * Remove the post from storage.
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post deleted.');
    }

    /**
     * Toggle the featured status of a post.
     */
    public function toggleFeature(Post $post): RedirectResponse
    {
        $this->authorize('update', $post);

        $post->update([
            'is_featured' => ! $post->is_featured
        ]);

        return redirect()
            ->back()
            ->with('success', $post->is_featured ? 'Post marked as featured!' : 'Post removed from featured.');
    }
}
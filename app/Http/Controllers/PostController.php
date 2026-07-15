<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Services\ContentSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
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
        // Redirect legacy ?category=ID links to clean /category/{slug} URLs (301 for SEO)
        if ($catId = $request->query('category')) {
            $cat = Category::find($catId);
            if ($cat) {
                return redirect()->route('categories.show', $cat)->setStatusCode(301);
            }
        }

        // 1. Get the latest featured post (cached 10 min)
        $featuredPost = Cache::remember('featured_post', 600, fn () =>
            Post::with('user', 'category')
                ->published()
                ->where('is_featured', true)
                ->latest()
                ->first()
        );

        // 2. Get the paginated posts (excluding featured, published only)
        $posts = Post::with(['user', 'category', 'likes'])
            ->published()
            ->when($featuredPost, fn ($q) => $q->where('id', '!=', $featuredPost->id))
            ->latest()
            ->filter($request->only(['search', 'category']))
            ->paginate(6)
            ->withQueryString();

        // 3. Categories (cached 1 hour)
        $categories = Cache::remember('nav_categories', 3600, fn () =>
            Category::orderBy('name')->get()
        );

        $latestPosts = Post::published()->latest()->take(5)->get();

        return view('posts.index', compact('posts', 'featuredPost', 'categories', 'latestPosts'));
    }

    /**
     * Show the form for creating a new post.
     */
    public function create()
    {
        $this->authorize('create', Post::class);

        $categories = Cache::remember('nav_categories', 3600, fn () =>
            Category::orderBy('name')->get()
        );

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
        $validated['content'] = app(ContentSanitizer::class)->sanitize($validated['content']);

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

        Cache::forget('nav_categories');

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
            'tags',
            'likes',
            'comments.user',
            'comments.likes',
        ]);

        $post->increment('views');

        $latestPosts = Post::published()->latest()->take(5)->get();
        $categories = Cache::remember('nav_categories', 3600, fn () =>
            Category::orderBy('name')->get()
        );

        return view('posts.show', compact('post', 'latestPosts', 'categories'));
    }

    /**
     * Show the form for editing.
     */
    public function edit(Post $post)
    {
        $this->authorize('update', $post);
        $categories = Cache::remember('nav_categories', 3600, fn () =>
            Category::orderBy('name')->get()
        );

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

        $validated['content'] = app(ContentSanitizer::class)->sanitize($validated['content']);

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

        Cache::forget('nav_categories');

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
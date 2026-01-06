@extends('layouts.app')

@section('title', $post->title)

@section('content')
<div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">

    {{-- ================= MAIN POST ================= --}}
    <div class="md:col-span-2 bg-white p-6 rounded shadow-md space-y-4">

        {{-- Title --}}
        <h1 class="text-3xl font-bold text-[#4B0082]">
            {{ $post->title }}
        </h1>

        {{-- Category --}}
        <p class="text-gray-500">
            Category:
            <span class="text-[#4B0082] font-medium">
                {{ $post->category }}
            </span>
        </p>

        {{-- Image --}}
        @if($post->image)
            <img
                src="{{ asset('storage/'.$post->image) }}"
                alt="Post Image"
                class="w-full h-64 object-cover rounded border border-[#4B0082] shadow-sm"
            >
        @endif

        {{-- Content --}}
        <p class="text-gray-800 whitespace-pre-line leading-relaxed">
            {{ $post->content }}
        </p>

        {{-- ================= ACTIONS ================= --}}
        <div class="flex flex-wrap gap-2 mt-6">

            <a href="{{ route('posts.index') }}"
               class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                Back to Posts
            </a>

            @can('update', $post)
                <a href="{{ route('posts.edit', $post) }}"
                   class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                    Edit Post
                </a>
            @endcan

            @can('delete', $post)
                <form action="{{ route('posts.destroy', $post) }}"
                      method="POST"
                      onsubmit="return confirm('Are you sure you want to delete this post?');">
                    @csrf
                    @method('DELETE')
                    <button
                        type="submit"
                        class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                        Delete
                    </button>
                </form>
            @endcan

        </div>
    </div>

    {{-- ================= SIDEBAR ================= --}}
    <aside class="bg-white p-6 rounded shadow-md space-y-6">

        {{-- Search --}}
        <div>
            <h3 class="text-[#4B0082] font-semibold mb-2">Search</h3>
            <form action="{{ route('posts.index') }}" method="GET">
                <input
                    type="text"
                    name="search"
                    placeholder="Search posts..."
                    value="{{ request('search') }}"
                    class="w-full border border-[#4B0082] rounded px-3 py-2
                           focus:outline-none focus:ring-2 focus:ring-[#4B0082]"
                >
                <button
                    type="submit"
                    class="mt-2 w-full bg-[#4B0082] text-white px-4 py-2
                           rounded hover:bg-indigo-900">
                    Search
                </button>
            </form>
        </div>

        {{-- Latest Posts --}}
        <div>
            <h3 class="text-[#4B0082] font-semibold mb-2">Latest Posts</h3>
            <ul class="space-y-1">
                @foreach($latestPosts as $latest)
                    <li>
                        <a href="{{ route('posts.show', $latest) }}"
                           class="text-indigo-600 hover:underline">
                            {{ \Illuminate\Support\Str::limit($latest->title, 35) }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Categories --}}
        <div>
            <h3 class="text-[#4B0082] font-semibold mb-2">Categories</h3>
            <ul class="space-y-1">
                @foreach($categories as $cat)
                    <li>
                        <a href="{{ route('posts.index', ['category' => $cat->category]) }}"
                           class="text-gray-700 hover:text-[#4B0082] hover:underline">
                            {{ $cat->category }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

    </aside>

</div>
@endsection

@extends('layouts.app')

@section('title', 'All Posts')

@section('content')
<div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">

    <!-- Main Posts Column -->
    <div class="md:col-span-2 bg-white p-6 rounded shadow-md">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-[#4B0082]">All Posts</h1>
            <a href="{{ route('posts.create') }}" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                Create New Post
            </a>
        </div>

        @if($posts->count())
            <div class="space-y-4">
                @foreach($posts as $post)
                <div class="border border-gray-300 rounded p-4 hover:shadow">
                    <h2 class="text-xl font-semibold text-[#4B0082]">{{ $post->title }}</h2>
                    <p class="text-gray-700">{{ Str::limit($post->content, 120) }}</p>
                    <div class="mt-2 flex justify-between items-center text-sm text-gray-500">
                        <span>{{ $post->category }}</span>
                        <a href="{{ route('posts.show', $post->id) }}" class="text-indigo-600 hover:underline">Read more</a>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $posts->links() }}
            </div>
        @else
            <p class="text-[#4B0082]">No posts available. <a href="{{ route('posts.create') }}" class="text-green-500 hover:underline">Create one</a>.</p>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="bg-white p-6 rounded shadow-md space-y-6">

        <!-- Search -->
        <div>
            <h3 class="text-[#4B0082] font-semibold mb-2">Search</h3>
            <form action="{{ route('posts.index') }}" method="GET">
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Search posts..." 
                    class="w-full border border-[#4B0082] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#4B0082]"
                    value="{{ request('search') }}"
                >
                <button type="submit" class="mt-2 w-full bg-[#4B0082] text-white px-4 py-2 rounded hover:bg-indigo-900">Search</button>
            </form>
        </div>

        <!-- Latest Posts -->
        <div>
            <h3 class="text-[#4B0082] font-semibold mb-2">Latest Posts</h3>
            <ul class="space-y-1">
                @foreach($latestPosts as $latest)
                <li>
                    <a href="{{ route('posts.show', $latest->id) }}" class="text-indigo-600 hover:underline">
                        {{ Str::limit($latest->title, 35) }}
                    </a>
                </li>
                @endforeach
            </ul>
        </div>

        <!-- Categories -->
        <div>
            <h3 class="text-[#4B0082] font-semibold mb-2">Categories</h3>
            <ul class="space-y-1">
                @foreach($categories as $cat)
                <li>
                    <a href="{{ route('posts.index', ['category' => $cat->category]) }}" class="text-gray-700 hover:text-[#4B0082] hover:underline">
                        {{ $cat->category }}
                    </a>
                </li>
                @endforeach
            </ul>
        </div>

    </div>

</div>
@endsection

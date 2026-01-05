@extends('layouts.app')

@section('title', 'Edit Post')

@section('content')
<div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">

    <!-- Main Form Column -->
    <div class="md:col-span-2 bg-white p-6 rounded shadow-md">
        <h2 class="text-2xl font-bold text-[#4B0082] mb-4">Edit Post</h2>
        @include('posts._form', ['post' => $post])
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
                @foreach(\App\Models\Post::latest()->take(5)->get() as $latest)
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
                @foreach(\App\Models\Post::select('category')->distinct()->get() as $cat)
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

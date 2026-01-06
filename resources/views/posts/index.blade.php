@extends('layouts.app')

@section('title', 'Home - Anim24.com')

@section('content')
<div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 px-4 py-8">

    {{-- ================= MAIN POSTS COLUMN ================= --}}
    <div class="md:col-span-2 space-y-6">
        <div class="flex justify-between items-center bg-white p-6 rounded shadow-sm border border-gray-100">
            <h1 class="text-2xl font-bold text-[#4B0082]">All Posts</h1>
            
            {{-- Senior Tip: Only show "Create" if user is authorized --}}
            @can('create', App\Models\Post::class)
                <a href="{{ route('posts.create') }}" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition shadow-sm">
                    Create New Post
                </a>
            @endcan
        </div>

        @if($posts->count())
            <div class="grid grid-cols-1 gap-6">
                @foreach($posts as $post)
                    <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow duration-300">
                        <div class="flex justify-between items-start mb-2">
                            <h2 class="text-xl font-bold text-[#4B0082]">
                                <a href="{{ route('posts.show', $post) }}" class="hover:text-indigo-600 transition">
                                    {{ $post->title }}
                                </a>
                            </h2>
                            <span class="text-xs font-medium px-2.5 py-0.5 rounded bg-indigo-100 text-[#4B0082]">
                                {{ $post->category ?? 'Uncategorized' }}
                            </span>
                        </div>

                        <p class="text-gray-600 leading-relaxed mb-4">
                            {{ Str::limit($post->content, 180) }}
                        </p>

                        <div class="flex justify-between items-center text-sm">
                            <div class="flex items-center text-gray-500 space-x-4">
                                <span><i class="far fa-user mr-1"></i> {{ $post->user->name ?? 'Author' }}</span>
                                <span><i class="far fa-calendar mr-1"></i> {{ $post->created_at->format('M d, Y') }}</span>
                            </div>
                            
                            <a href="{{ route('posts.show', $post) }}" class="font-semibold text-indigo-600 hover:text-indigo-800 flex items-center">
                                Read more 
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $posts->links() }}
            </div>
        @else
            <div class="bg-white p-12 rounded shadow-md text-center">
                <p class="text-gray-500 text-lg">No posts available matching your criteria.</p>
                @can('create', App\Models\Post::class)
                    <a href="{{ route('posts.create') }}" class="mt-4 inline-block text-green-500 font-semibold hover:underline">
                        Be the first to create one!
                    </a>
                @endcan
            </div>
        @endif
    </div>

    {{-- ================= SIDEBAR ================= --}}
    <aside class="space-y-6">

        <div class="bg-white p-6 rounded shadow-sm border border-gray-100">
            <h3 class="text-[#4B0082] font-bold text-lg mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                Search
            </h3>
            <form action="{{ route('posts.index') }}" method="GET">
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Keywords..." 
                    class="w-full border border-gray-300 rounded px-4 py-2 focus:ring-2 focus:ring-[#4B0082] focus:border-transparent outline-none transition"
                    value="{{ request('search') }}"
                >
                <button type="submit" class="mt-3 w-full bg-[#4B0082] text-white px-4 py-2 rounded font-medium hover:bg-indigo-900 transition">
                    Find Posts
                </button>
            </form>
        </div>

        <div class="bg-white p-6 rounded shadow-sm border border-gray-100">
            <h3 class="text-[#4B0082] font-bold text-lg mb-4">Latest Updates</h3>
            <ul class="divide-y divide-gray-100">
                @foreach($latestPosts as $latest)
                <li class="py-3">
                    <a href="{{ route('posts.show', $latest) }}" class="text-gray-700 hover:text-indigo-600 transition text-sm font-medium block">
                        {{ Str::limit($latest->title, 45) }}
                    </a>
                    <span class="text-xs text-gray-400">{{ $latest->created_at->diffForHumans() }}</span>
                </li>
                @endforeach
            </ul>
        </div>

        <div class="bg-white p-6 rounded shadow-sm border border-gray-100">
            <h3 class="text-[#4B0082] font-bold text-lg mb-4">Categories</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($categories as $cat)
                <a href="{{ route('posts.index', ['category' => $cat->category]) }}" 
                   class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs hover:bg-[#4B0082] hover:text-white transition">
                    {{ $cat->category }}
                </a>
                @endforeach
            </div>
        </div>

    </aside>

</div>
@endsection
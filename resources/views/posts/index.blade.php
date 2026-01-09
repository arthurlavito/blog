@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-50/50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header & Search --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
            <div>
                <h1 class="text-5xl font-black text-gray-900 tracking-tighter">Explore <span class="text-indigo-500">News</span></h1>
                <p class="text-gray-400 font-bold uppercase text-[10px] tracking-[0.3em] mt-2">Latest updates from the Anim24 community</p>
            </div>
            
            <form action="{{ route('posts.index') }}" method="GET" class="relative w-full md:w-96">
                <input type="text" name="search" placeholder="Search articles..." value="{{ request('search') }}"
                       class="w-full bg-white border-none rounded-2xl px-6 py-4 text-sm shadow-sm focus:ring-2 focus:ring-[#4B0082] transition">
                <button type="submit" class="absolute right-4 top-4 text-gray-300 hover:text-[#4B0082]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
            </form>
        </div>

        {{-- The Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($posts as $post)
                <article class="group bg-white rounded-[2.5rem] border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 overflow-hidden flex flex-col">
                    
                    {{-- Thumbnail Container --}}
                    <div class="relative h-64 overflow-hidden">
                        @if($post->image)
                            <img src="{{ asset('storage/' . $post->image) }}" 
                                 alt="{{ $post->title }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full bg-indigo-50 flex items-center justify-center">
                                <span class="text-[#4B0082] font-black text-4xl opacity-20">ANIM24</span>
                            </div>
                        @endif
                        
                        {{-- Category Badge --}}
                        <div class="absolute top-5 left-5">
                            <span class="px-4 py-1.5 bg-white/90 backdrop-blur text-[#4B0082] text-[10px] font-black uppercase tracking-widest rounded-xl shadow-sm">
                                {{ $post->category }}
                            </span>
                        </div>
                    </div>

                    {{-- Card Content --}}
                    <div class="p-8 flex flex-col flex-grow">
                        <div class="flex items-center gap-2 mb-4 text-[10px] font-black uppercase text-gray-400 tracking-widest">
                            <span>{{ $post->user->name }}</span>
                            <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                            <span>{{ $post->created_at->diffForHumans() }}</span>
                        </div>

                        <h2 class="text-xl font-black text-gray-900 leading-tight group-hover:text-[#4B0082] transition-colors mb-4">
                            <a href="{{ route('posts.show', $post) }}">
                                {{ \Illuminate\Support\Str::limit($post->title, 60) }}
                            </a>
                        </h2>

                        <p class="text-sm text-gray-500 font-medium line-clamp-3 mb-6">
                            {{ $post->content }}
                        </p>

                        <div class="mt-auto">
                            <a href="{{ route('posts.show', $post) }}" class="inline-flex items-center text-[10px] font-black uppercase tracking-[0.2em] text-[#4B0082] group-hover:gap-3 transition-all">
                                Read Article 
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full py-20 text-center">
                    <p class="text-gray-400 font-bold uppercase tracking-widest">No articles found matching your criteria.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-16">
            {{ $posts->links() }}
        </div>
    </div>
</div>
@endsection
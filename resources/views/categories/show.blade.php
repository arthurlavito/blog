@extends('layouts.app')

@section('title', $category->name . ' News | Anim24')
@section('meta_description', 'Browse all ' . $category->name . ' articles from Anim24.')
@section('canonical', route('categories.show', $category))

@section('content')
<div class="py-12 bg-gray-50/50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-12">
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-[#4B0082] mb-2">Category</p>
            <h1 class="text-5xl font-black text-gray-900 tracking-tighter">
                {{ $category->name }} <span class="text-indigo-500">News</span>
            </h1>
            <p class="text-gray-400 font-bold uppercase text-[10px] tracking-[0.3em] mt-2">
                {{ $posts->total() }} article{{ $posts->total() !== 1 ? 's' : '' }}
            </p>
        </div>

        {{-- Post Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($posts as $post)
                <article class="group bg-white rounded-[2.5rem] border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 overflow-hidden flex flex-col">

                    <div class="relative h-64 overflow-hidden">
                        @if($post->image)
                            <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" loading="lazy"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full bg-indigo-50 flex items-center justify-center">
                                <span class="text-[#4B0082] font-black text-4xl opacity-20">ANIM24</span>
                            </div>
                        @endif
                    </div>

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

                        <p class="text-sm text-gray-500 font-medium line-clamp-3 mb-6 leading-relaxed">
                            {{ \Illuminate\Support\Str::limit(strip_tags($post->content), 120) }}
                        </p>

                        <div class="mt-auto">
                            <a href="{{ route('posts.show', $post) }}"
                               class="inline-flex items-center text-[10px] font-black uppercase tracking-[0.2em] text-[#4B0082] group-hover:gap-3 transition-all">
                                Read Article
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full py-24 text-center bg-white rounded-[3rem] border-2 border-dashed border-gray-100">
                    <h3 class="text-2xl font-black text-gray-900 mb-2">No Articles Yet</h3>
                    <p class="text-gray-400 text-sm mb-6">Nothing published in {{ $category->name }} yet.</p>
                    <a href="{{ route('posts.index') }}"
                       class="inline-flex items-center px-8 py-4 bg-[#4B0082] text-white text-[10px] font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-indigo-100">
                        View All News
                    </a>
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

@extends('layouts.app')

@section('title', request('search')
    ? 'Search: "' . request('search') . '" | Anim24'
    : (request('category')
        ? (optional(\App\Models\Category::find(request('category')))->name . ' News | Anim24')
        : 'Breaking News & Global Analysis | Anim24'))
@section('meta_description', 'Latest breaking news, anime, sports and global analysis from Anim24.')
@section('canonical', route('posts.index'))

@section('content')
<div class="py-12 bg-gray-50/50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- 1. Header & Search --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-6">
            <div>
                <h1 class="text-5xl font-black text-gray-900 tracking-tighter">
                    @if(request('search'))
                        Search <span class="text-indigo-500">Results</span>
                    @elseif(request('category'))
                        Category: <span class="text-indigo-500">{{ \App\Models\Category::find(request('category'))->name ?? 'News' }}</span>
                    @else
                        Explore <span class="text-indigo-500">News</span>
                    @endif
                </h1>
                <p class="text-gray-400 font-bold uppercase text-[10px] tracking-[0.3em] mt-2">
                    {{ request('search') ? 'Finding the best matches for "' . request('search') . '"' : 'Latest updates from the Anim24 community' }}
                </p>
            </div>
            
            <div class="flex flex-col items-end gap-2">
                <form action="{{ route('posts.index') }}" method="GET" class="relative w-full md:w-96">
                    <input type="text" name="search" placeholder="Search articles..." value="{{ request('search') }}"
                           class="w-full bg-white border-none rounded-2xl px-6 py-4 text-sm shadow-sm focus:ring-2 focus:ring-[#4B0082] transition">
                    <button type="submit" class="absolute right-4 top-4 text-gray-300 hover:text-[#4B0082]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                </form>
                @if(request('search') || request('category'))
                    <a href="{{ route('posts.index') }}" class="text-[10px] font-black uppercase tracking-widest text-rose-500 hover:text-rose-700 transition">
                        ✕ Clear Filters
                    </a>
                @endif
            </div>
        </div>

        {{-- 2. Category Filter Bar (Primary Filter) --}}
        <div class="flex items-center gap-3 mb-12 overflow-x-auto pb-4 no-scrollbar">
            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mr-4">Filter By:</span>
            
            <a href="{{ route('posts.index') }}" 
               class="px-6 py-2 rounded-full text-[10px] font-black uppercase tracking-widest transition-all
               {{ !request('category') ? 'bg-[#4B0082] text-white shadow-lg shadow-indigo-100' : 'bg-white text-gray-500 border border-gray-100 hover:bg-gray-50' }}">
                All News
            </a>

            @foreach($categories as $cat)
                <a href="{{ route('posts.index', ['category' => $cat->id]) }}"
                   class="px-6 py-2 rounded-full text-[10px] font-black uppercase tracking-widest transition-all
                   {{ request('category') == $cat->id ? 'bg-[#4B0082] text-white shadow-lg shadow-indigo-100' : 'bg-white text-gray-500 border border-gray-100 hover:bg-gray-50' }}">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>

        {{-- 3. Featured Hero (Hidden when filtering) --}}
        @if($featuredPost && !request('search') && !request('category'))
        <div class="mb-16 relative group">
            <div class="relative h-[500px] w-full overflow-hidden rounded-[3rem] shadow-2xl">
                @if($featuredPost->image)
                    <img src="{{ asset('storage/' . $featuredPost->image) }}" alt="{{ $featuredPost->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-700">
                @else
                    <div class="w-full h-full bg-slate-900 flex items-center justify-center">
                        <span class="text-white/10 font-black text-9xl">ANIM24</span>
                    </div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-transparent"></div>

                <div class="absolute bottom-0 left-0 p-12 w-full md:w-2/3">
                    <span class="px-4 py-2 bg-rose-600 text-white text-[10px] font-black uppercase tracking-[0.3em] rounded-full mb-6 inline-block">
                        Featured {{ $featuredPost->category->name ?? 'Story' }}
                    </span>
                    <h1 class="text-4xl md:text-6xl font-black text-white mb-6 leading-tight tracking-tighter">
                        {{ $featuredPost->title }}
                    </h1>
                    <a href="{{ route('posts.show', $featuredPost) }}" class="inline-flex items-center px-8 py-4 bg-white text-black text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-rose-500 hover:text-white transition shadow-xl">
                        Read Full Feature
                    </a>
                </div>
            </div>
        </div>
        @endif

        {{-- 4. Post Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($posts as $post)
                <article class="group bg-white rounded-[2.5rem] border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 overflow-hidden flex flex-col">
                    
                    <div class="relative h-64 overflow-hidden">
                        @if($post->image)
                            <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full bg-indigo-50 flex items-center justify-center">
                                <span class="text-[#4B0082] font-black text-4xl opacity-20">ANIM24</span>
                            </div>
                        @endif
                        
                        @if($post->category)
                        <div class="absolute top-5 left-5">
                            <span class="px-4 py-1.5 bg-white/90 backdrop-blur text-[#4B0082] text-[10px] font-black uppercase tracking-widest rounded-xl shadow-sm">
                                {{ $post->category->name }}
                            </span>
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
                            {{ Str::limit(strip_tags($post->content), 120) }}
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
                <div class="col-span-full py-24 text-center bg-white rounded-[3rem] border-2 border-dashed border-gray-100">
                    <h3 class="text-2xl font-black text-gray-900 mb-2">No Articles Found</h3>
                    <p class="text-gray-400 text-sm mb-6">Try adjusting your search or category filters.</p>
                    <a href="{{ route('posts.index') }}" class="inline-flex items-center px-8 py-4 bg-[#4B0082] text-white text-[10px] font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-indigo-100">
                        View All News
                    </a>
                </div>
            @endforelse
        </div>

        {{-- 5. Pagination --}}
        <div class="mt-16">
            {{ $posts->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
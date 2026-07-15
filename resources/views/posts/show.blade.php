@extends('layouts.app')

@section('title', ($post->meta_title ?: $post->title) . ' | Anim24')
@section('meta_description', $post->meta_description ?: \Illuminate\Support\Str::words(strip_tags($post->content), 28, '…'))
@section('canonical', $post->canonical_url ?: route('posts.show', $post))
@if($post->noindex)
@section('robots', 'noindex,follow')
@endif
@section('og_title', $post->title)
@section('og_image', $post->image ? asset('storage/'.$post->image) : asset('images/logo.png'))
@section('og_type', 'article')

@section('content')
<div class="py-10 bg-gray-50/50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- ================= MAIN POST CONTENT ================= --}}
            <div class="lg:col-span-2 space-y-8">
                <article class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
                    
                    {{-- Featured Image --}}
                    @if($post->image)
                        <div class="relative h-96 w-full">
                            <img src="{{ asset('storage/'.$post->image) }}"
                                 alt="{{ $post->title }}"
                                 loading="lazy"
                                 class="w-full h-full object-cover">
                            <div class="absolute top-6 left-6">
                                @if($post->category)
                                    <a href="{{ route('categories.show', $post->category) }}"
                                       class="px-4 py-2 bg-[#4B0082] text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg hover:bg-indigo-900 transition">
                                        {{ $post->category->name }}
                                    </a>
                                @else
                                    <span class="px-4 py-2 bg-[#4B0082] text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg">
                                        Uncategorized
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="p-8 md:p-12">
                        {{-- Breadcrumbs --}}
                        <x-breadcrumbs :items="[
                            ['label' => 'Home',                                                  'url' => route('home')],
                            ['label' => $post->category->name ?? 'Uncategorized',                'url' => $post->category ? route('categories.show', $post->category) : route('home')],
                            ['label' => \Illuminate\Support\Str::limit($post->title, 50),        'url' => route('posts.show', $post)],
                        ]" />

                        {{-- Title --}}
                        <h1 class="text-4xl md:text-5xl font-black text-gray-900 leading-tight tracking-tighter mb-6">
                            {{ $post->title }}
                        </h1>

                        {{-- Metadata --}}
                        <div class="flex items-center gap-4 mb-10 pb-8 border-b border-gray-50">
                            <img class="h-10 w-10 rounded-full border-2 border-indigo-100"
                                 src="https://ui-avatars.com/api/?name={{ urlencode($post->user->name) }}&background=4B0082&color=fff&bold=true"
                                 alt="" loading="lazy">
                            <div>
                                <p class="text-sm font-black text-gray-800 leading-none">{{ $post->user->name }}</p>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">
                                    {{ $post->created_at->format('M d, Y') }}
                                </p>
                            </div>
                        </div>

                        {{-- Tags --}}
                        @if($post->tags->isNotEmpty())
                        <div class="flex flex-wrap gap-2 mb-8">
                            @foreach($post->tags as $tag)
                                <a href="{{ route('tags.show', $tag) }}"
                                   class="px-4 py-1.5 bg-indigo-50 text-[#4B0082] text-[10px] font-black uppercase tracking-widest rounded-full hover:bg-indigo-100 transition">
                                    #{{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                        @endif

                        {{-- Body Content — already purified by ContentSanitizer on save --}}
                        <div class="post-body mb-12">
                            {!! $post->content !!}
                        </div>

                        {{-- ================= POST REACTIONS ================= --}}
                        @php
                            $reactionCounts = $post->likes()->selectRaw('type, count(*) as total')->groupBy('type')->pluck('total','type');
                            $userReaction = auth()->check() ? optional($post->likes()->where('user_id', auth()->id())->first())->type : null;
                        @endphp
                        <div x-data="{
                            userReaction: {{ $userReaction ? '\''. $userReaction .'\'' : 'null' }},
                            counts: {{ $reactionCounts->toJson() }},
                            total() { return Object.values(this.counts).reduce((a,b)=>a+b,0); },
                            react(type) {
                                if (!{{ auth()->check() ? 'true' : 'false' }}) { window.location.href='{{ route('login') }}'; return; }
                                fetch('{{ route('posts.like') }}', {
                                    method: 'POST',
                                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
                                    body: JSON.stringify({ id: {{ $post->id }}, type: 'post', reaction: type })
                                }).then(r=>r.json()).then(data => {
                                    this.userReaction = data.user_reaction;
                                    this.counts = data.counts;
                                });
                            }
                        }" class="flex flex-col md:flex-row items-center justify-between py-6 border-t border-b border-gray-50 mb-8 gap-6">
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 mr-2">React:</span>
                                @foreach(['like'=>'❤️','love'=>'😍','fire'=>'🔥','thinking'=>'🤔'] as $type => $emoji)
                                <button @click="react('{{ $type }}')"
                                        :class="userReaction === '{{ $type }}' ? 'bg-indigo-100 ring-2 ring-[#4B0082]' : 'bg-gray-50 hover:bg-indigo-50'"
                                        class="flex items-center gap-1.5 px-4 py-2.5 rounded-2xl transition-all active:scale-95 text-sm">
                                    <span>{{ $emoji }}</span>
                                    <span class="text-[10px] font-black text-gray-600" x-text="counts['{{ $type }}'] || 0"></span>
                                </button>
                                @endforeach
                                <span class="ml-2 text-[10px] font-black uppercase tracking-widest text-gray-400" x-text="total() + ' Hype'"></span>
                            </div>

                            {{-- Social Share --}}
                            <div class="flex items-center gap-3">
                                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400 mr-2">Share:</span>
                                <a href="https://twitter.com/intent/tweet?url={{ url()->current() }}" target="_blank" class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center hover:bg-black hover:text-white transition shadow-sm">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                </a>
                                <button onclick="navigator.clipboard.writeText(window.location.href); alert('Link copied!')" class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center hover:bg-emerald-500 hover:text-white transition shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                </button>
                            </div>
                        </div>

                        {{-- Admin Controls --}}
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('posts.index') }}" class="px-6 py-3 bg-gray-100 text-gray-500 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-gray-200 transition">← Back to Feed</a>
                            @can('update', $post)
                                <a href="{{ route('posts.edit', $post) }}" class="px-6 py-3 bg-amber-400 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-amber-500 transition shadow-lg shadow-amber-100">Edit Post</a>
                            @endcan
                        </div>

                        {{-- ── Prev / Next Navigation ── --}}
                        @if($prevPost || $nextPost)
                        <div class="mt-10 pt-8 border-t border-gray-50 grid grid-cols-2 gap-4">
                            <div>
                                @if($prevPost)
                                <a href="{{ route('posts.show', $prevPost) }}"
                                   class="group flex flex-col gap-1 p-4 bg-gray-50 rounded-2xl hover:bg-indigo-50 transition">
                                    <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 group-hover:text-[#4B0082]">← Previous</span>
                                    <span class="text-sm font-black text-gray-700 group-hover:text-[#4B0082] leading-snug line-clamp-2">
                                        {{ \Illuminate\Support\Str::limit($prevPost->title, 55) }}
                                    </span>
                                </a>
                                @endif
                            </div>
                            <div>
                                @if($nextPost)
                                <a href="{{ route('posts.show', $nextPost) }}"
                                   class="group flex flex-col gap-1 p-4 bg-gray-50 rounded-2xl hover:bg-indigo-50 transition text-right">
                                    <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 group-hover:text-[#4B0082]">Next →</span>
                                    <span class="text-sm font-black text-gray-700 group-hover:text-[#4B0082] leading-snug line-clamp-2">
                                        {{ \Illuminate\Support\Str::limit($nextPost->title, 55) }}
                                    </span>
                                </a>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </article>

                {{-- ── Related Articles ── --}}
                @if($relatedPosts->isNotEmpty())
                <div class="mt-8">
                    <h3 class="text-xl font-black text-gray-900 uppercase tracking-tighter mb-6">
                        Related <span class="text-[#4B0082]">Articles</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        @foreach($relatedPosts as $related)
                        <article class="group bg-white rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col">
                            <div class="relative h-40 overflow-hidden">
                                @if($related->image)
                                    <img src="{{ asset('storage/'.$related->image) }}" alt="{{ $related->title }}"
                                         loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full bg-indigo-50 flex items-center justify-center">
                                        <span class="text-[#4B0082] font-black text-2xl opacity-20">ANIM24</span>
                                    </div>
                                @endif
                                @if($related->category)
                                <div class="absolute top-3 left-3">
                                    <span class="px-3 py-1 bg-white/90 backdrop-blur text-[#4B0082] text-[9px] font-black uppercase tracking-widest rounded-lg">
                                        {{ $related->category->name }}
                                    </span>
                                </div>
                                @endif
                            </div>
                            <div class="p-5 flex flex-col flex-grow">
                                <h4 class="text-sm font-black text-gray-900 leading-snug group-hover:text-[#4B0082] transition-colors mb-3 line-clamp-2">
                                    <a href="{{ route('posts.show', $related) }}">{{ $related->title }}</a>
                                </h4>
                                <div class="mt-auto text-[9px] font-black uppercase text-gray-400 tracking-widest">
                                    {{ $related->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </article>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- ================= DISCUSSION HUB ================= --}}
                <div class="mt-12 space-y-8">
                    <h3 class="text-xl font-black text-gray-900 uppercase tracking-tighter">
                        Discussion <span class="text-[#4B0082] ml-2">({{ $post->comments->count() }})</span>
                    </h3>

                    @auth
                        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm">
                            <form action="{{ route('comments.store', $post) }}" method="POST">
                                @csrf
                                {{-- Honeypot: hidden from real users, bots fill it --}}
                                <div style="display:none" aria-hidden="true">
                                    <input type="text" name="website" tabindex="-1" autocomplete="off">
                                </div>
                                <div class="flex gap-4">
                                    <img class="h-10 w-10 rounded-full hidden md:block" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=4B0082&color=fff&bold=true" alt="" loading="lazy">
                                    <div class="flex-1 space-y-4">
                                        <textarea name="body" rows="3" required placeholder="What are your thoughts?" class="w-full bg-gray-50 border-none rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-[#4B0082] transition placeholder-gray-400"></textarea>
                                        <div class="flex justify-end">
                                            <button type="submit" class="bg-[#4B0082] text-white px-8 py-3 rounded-xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-indigo-900 transition shadow-lg shadow-indigo-100">Post Comment</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endauth

                    {{-- Comments List --}}
                    <div class="space-y-4">
                        @forelse($post->comments as $comment)
                            <div class="bg-white p-4 rounded-3xl border border-gray-50 shadow-sm transition-all hover:border-indigo-100">
                                <div class="flex items-start gap-3">
                                    <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($comment->user->name) }}&background=f3f4f6&color=4B0082&bold=true" alt="" loading="lazy">
                                    
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-1">
                                            <h4 class="text-xs font-black text-gray-900 truncate">{{ $comment->user->name }}</h4>
                                            <span class="text-[9px] font-bold text-gray-300 uppercase tracking-widest">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                        
                                        <p class="text-gray-600 text-xs leading-relaxed mb-3">
                                            {{ $comment->body }}
                                        </p>

                                        {{-- Comment like --}}
                                        <div x-data="{
                                            liked: {{ (auth()->check() && $comment->isLikedBy(auth()->user())) ? 'true' : 'false' }},
                                            count: {{ $comment->likes()->count() }},
                                            toggle() {
                                                if (!{{ auth()->check() ? 'true' : 'false' }}) { window.location.href='{{ route('login') }}'; return; }
                                                fetch('{{ route('posts.like') }}', {
                                                    method: 'POST',
                                                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
                                                    body: JSON.stringify({ id: {{ $comment->id }}, type: 'comment', reaction: 'like' })
                                                }).then(r=>r.json()).then(data => {
                                                    this.liked = data.user_reaction !== null;
                                                    this.count = data.total;
                                                });
                                            }
                                        }" class="flex items-center gap-2">
                                            <button @click="toggle"
                                                    :class="liked ? 'text-rose-500 bg-rose-50' : 'text-gray-400 bg-gray-50 hover:text-rose-500'"
                                                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-full transition-all text-[10px] font-black uppercase tracking-tighter">
                                                <svg class="w-3 h-3" :class="liked ? 'fill-current' : 'fill-none'" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                                </svg>
                                                <span x-text="count"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-400 text-xs py-4">No comments yet. Start the hype!</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- ================= SIDEBAR ================= --}}
            <aside class="space-y-8">
                <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm">
                    <h3 class="text-xs font-black uppercase text-gray-400 tracking-[0.2em] mb-4">Quick Search</h3>
                    <form action="{{ route('posts.index') }}" method="GET" class="relative">
                        <input type="text" name="search" placeholder="Search Anim24..." value="{{ request('search') }}" class="w-full bg-gray-50 border-none rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-[#4B0082] transition">
                        <button type="submit" class="absolute right-3 top-3 p-2 text-gray-300 hover:text-[#4B0082]"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg></button>
                    </form>
                </div>

                <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm">
                    <h3 class="text-xs font-black uppercase text-gray-400 tracking-[0.2em] mb-6 flex items-center"><span class="w-1.5 h-1.5 bg-indigo-500 rounded-full mr-2"></span>Trending Now</h3>
                    <ul class="space-y-6">
                        @foreach($latestPosts as $latest)
                            <li class="group">
                                <a href="{{ route('posts.show', $latest) }}" class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-800 group-hover:text-[#4B0082] transition-colors leading-tight">{{ \Illuminate\Support\Str::limit($latest->title, 50) }}</span>
                                    <span class="text-[10px] font-black uppercase text-gray-300 mt-2 tracking-widest">{{ $latest->created_at->diffForHumans() }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                @if($morePosts->isNotEmpty())
                <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm">
                    <h3 class="text-xs font-black uppercase text-gray-400 tracking-[0.2em] mb-6 flex items-center">
                        <span class="w-1.5 h-1.5 bg-rose-500 rounded-full mr-2"></span>
                        More from {{ $post->category->name }}
                    </h3>
                    <ul class="space-y-5">
                        @foreach($morePosts as $more)
                        <li class="group">
                            <a href="{{ route('posts.show', $more) }}" class="flex flex-col">
                                <span class="text-sm font-bold text-gray-800 group-hover:text-[#4B0082] transition-colors leading-tight">
                                    {{ \Illuminate\Support\Str::limit($more->title, 55) }}
                                </span>
                                <span class="text-[10px] font-black uppercase text-gray-300 mt-1.5 tracking-widest">
                                    {{ $more->created_at->diffForHumans() }}
                                </span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm">
                    <h3 class="text-xs font-black uppercase text-gray-400 tracking-[0.2em] mb-6 flex items-center"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-2"></span>Topics</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($categories as $cat)
                            <a href="{{ route('categories.show', $cat) }}" class="px-4 py-2 bg-gray-50 rounded-xl text-[11px] font-black text-gray-500 uppercase tracking-tighter hover:bg-indigo-50 hover:text-[#4B0082] transition">#{{ $cat->name }}</a>
                        @endforeach
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@php
use Illuminate\Support\Str;

$keywords = collect();
if ($post->tags->isNotEmpty()) {
    $keywords = $post->tags->pluck('name');
}
if ($post->focus_keyword) {
    $keywords->push($post->focus_keyword);
}

$newsArticle = [
    '@context'         => 'https://schema.org',
    '@type'            => 'NewsArticle',
    'headline'         => $post->meta_title ?: $post->title,
    'image'            => [$post->image ? asset('storage/'.$post->image) : asset('images/logo.png')],
    'datePublished'    => $post->created_at->toIso8601String(),
    'dateModified'     => $post->updated_at->toIso8601String(),
    'author'           => ['@type' => 'Person', 'name' => $post->user->name],
    'publisher'        => ['@id' => config('app.url') . '/#organization'],
    'description'      => $post->meta_description ?: Str::words(strip_tags($post->content), 28, '…'),
    'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => route('posts.show', $post)],
];

if ($post->category) {
    $newsArticle['articleSection'] = $post->category->name;
}
if ($keywords->isNotEmpty()) {
    $newsArticle['keywords'] = $keywords->unique()->implode(', ');
}
@endphp
<script type="application/ld+json">{!! json_encode($newsArticle, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

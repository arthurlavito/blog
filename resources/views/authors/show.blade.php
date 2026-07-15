@extends('layouts.app')

@section('title', $user->name . ' | Anim24')
@section('meta_description', $user->bio ? \Illuminate\Support\Str::limit(strip_tags($user->bio), 155) : 'Articles by ' . $user->name . ' on Anim24.')
@section('canonical', route('authors.show', $user))

@section('content')
<div class="py-12 bg-gray-50/50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Author Header --}}
        <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm p-10 mb-10 flex flex-col md:flex-row items-center md:items-start gap-8">
            <div class="w-24 h-24 rounded-full bg-indigo-100 flex items-center justify-center text-3xl font-black text-[#4B0082] shrink-0">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="flex-1 text-center md:text-left">
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">{{ $user->name }}</h1>
                <p class="text-xs font-black uppercase tracking-widest text-indigo-500 mt-1">Author · {{ $posts->total() }} {{ $posts->total() === 1 ? 'Article' : 'Articles' }}</p>
                @if($user->bio)
                    <p class="mt-4 text-gray-600 text-sm leading-relaxed max-w-2xl">{{ $user->bio }}</p>
                @endif
            </div>
        </div>

        {{-- JSON-LD Person schema --}}
        @php
        $person = [
            '@context' => 'https://schema.org',
            '@type'    => 'Person',
            'name'     => $user->name,
            'url'      => route('authors.show', $user),
        ];
        @endphp
        <script type="application/ld+json">{!! json_encode($person, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

        {{-- Posts Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($posts as $post)
                <a href="{{ route('posts.show', $post) }}" class="group bg-white rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-md transition-all overflow-hidden">
                    @if($post->image)
                        <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="w-full h-44 object-cover group-hover:opacity-90 transition-opacity">
                    @else
                        <div class="w-full h-44 bg-indigo-50 flex items-center justify-center text-indigo-200 font-black text-2xl">A24</div>
                    @endif
                    <div class="p-6">
                        @if($post->category)
                            <span class="text-[9px] font-black uppercase tracking-widest text-indigo-500">{{ $post->category->name }}</span>
                        @endif
                        <h2 class="text-sm font-black text-gray-900 mt-1 group-hover:text-[#4B0082] transition-colors line-clamp-2">{{ $post->title }}</h2>
                        <p class="text-[10px] text-gray-400 font-bold uppercase mt-3">{{ $post->created_at->format('M d, Y') }}</p>
                    </div>
                </a>
            @empty
                <div class="col-span-3 py-20 text-center">
                    <p class="text-sm font-bold text-gray-400">No published articles yet.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($posts->hasPages())
            <div class="mt-10">{{ $posts->links() }}</div>
        @endif

    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Create Post')

@section('content')
<div class="py-10 bg-gray-50/50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Main Form Column --}}
            <div class="lg:col-span-2">
                <div class="bg-white p-8 md:p-10 rounded-[2.5rem] border border-gray-100 shadow-sm">
                    <div class="mb-8">
                        <h2 class="text-3xl font-black text-gray-900 tracking-tight">Create New Post</h2>
                        <p class="text-sm text-gray-500 font-medium mt-1">Publish your latest news to the Anim24 community.</p>
                    </div>
                    
                    @include('posts._form')
                </div>
            </div>

            {{-- Sidebar --}}
            <aside class="space-y-8">
                {{-- Search Card --}}
                <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm">
                    <h3 class="text-xs font-black uppercase text-gray-400 tracking-[0.2em] mb-4">Quick Search</h3>
                    <form action="{{ route('posts.index') }}" method="GET" class="relative">
                        <input type="text" name="search" placeholder="Type keywords..." 
                               class="w-full bg-gray-50 border-none rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-[#4B0082] transition">
                    </form>
                </div>

                {{-- Latest Posts Card --}}
                <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm">
                    <h3 class="text-xs font-black uppercase text-gray-400 tracking-[0.2em] mb-6 flex items-center">
                        <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full mr-2"></span>
                        Recent News
                    </h3>
                    <ul class="space-y-4">
                        @foreach(\App\Models\Post::latest()->take(5)->get() as $latest)
                            <li>
                                <a href="{{ route('posts.show', $latest->id) }}" class="group flex flex-col">
                                    <span class="text-sm font-bold text-gray-800 group-hover:text-[#4B0082] transition-colors leading-tight">
                                        {{ Str::limit($latest->title, 45) }}
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </aside>

        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', 'Edit Post')

@section('content')
<div class="py-10 bg-gray-50/50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Main Form Column --}}
            <div class="lg:col-span-2">
                <div class="bg-white p-8 md:p-10 rounded-[2.5rem] border border-gray-100 shadow-sm">
                    <div class="mb-8 flex justify-between items-end">
                        <div>
                            <h2 class="text-3xl font-black text-gray-900 tracking-tight">Edit Post</h2>
                            <p class="text-sm text-gray-500 font-medium mt-1">Updating: <span class="text-[#4B0082]">{{ $post->title }}</span></p>
                        </div>
                        <a href="{{ route('posts.show', $post) }}" class="text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-[#4B0082] transition">
                            View Original
                        </a>
                    </div>
                    
                    @include('posts._form', ['post' => $post])
                </div>
            </div>

            {{-- Sidebar --}}
            <aside class="space-y-8">
                {{-- Status Card --}}
                <div class="bg-[#4B0082] p-8 rounded-[2rem] shadow-xl shadow-indigo-100 text-white">
                    <h3 class="text-[10px] font-black uppercase tracking-[0.2em] mb-4 opacity-70">Post Statistics</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold">Created</span>
                            <span class="text-xs opacity-80">{{ $post->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold">Last Updated</span>
                            <span class="text-xs opacity-80">{{ $post->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>

                {{-- Quick Links Card --}}
                <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm">
                    <h3 class="text-xs font-black uppercase text-gray-400 tracking-[0.2em] mb-6">Recent News</h3>
                    <ul class="space-y-4">
                        @foreach(\App\Models\Post::latest()->take(3)->get() as $latest)
                            <li>
                                <a href="{{ route('posts.show', $latest->id) }}" class="group flex flex-col">
                                    <span class="text-sm font-bold text-gray-800 group-hover:text-[#4B0082] transition-colors leading-tight">
                                        {{ Str::limit($latest->title, 40) }}
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
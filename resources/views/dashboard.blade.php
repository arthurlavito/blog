@extends('layouts.app') {{-- This points to your app.blade --}}

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
            
            <div class="mb-8 border-b pb-4">
                <h3 class="text-2xl font-bold text-gray-800">Welcome, {{ auth()->user()->name }}!</h3>
                <p class="text-gray-500">Role: <span class="font-bold text-indigo-600">{{ ucfirst(auth()->user()->role) }}</span></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                {{-- ADMIN CARD --}}
                @if(auth()->user()->isAdmin())
                    <div class="border-2 border-indigo-500 rounded-2xl p-6 bg-indigo-50 shadow-sm">
                        <h4 class="text-indigo-900 font-bold uppercase text-xs mb-2">Administration</h4>
                        <p class="text-sm text-gray-600 mb-4">Manage the community, promote users, and handle permissions.</p>
                        <a href="{{ route('admin.users.index') }}" class="block text-center bg-indigo-600 text-white py-2 rounded-lg font-semibold hover:bg-indigo-700 transition">
                            Manage Users
                        </a>
                    </div>
                @endif

                {{-- AUTHOR/ADMIN CARD --}}
                @if(auth()->user()->isAuthor() || auth()->user()->isAdmin())
                    <div class="border border-emerald-200 rounded-2xl p-6 bg-emerald-50">
                        <h4 class="text-emerald-900 font-bold uppercase text-xs mb-2">Content</h4>
                        <p class="text-sm text-gray-600 mb-4">You have permission to write and edit blog posts.</p>
                        <a href="{{ route('posts.create') }}" class="block text-center bg-emerald-600 text-white py-2 rounded-lg font-semibold hover:bg-emerald-700 transition">
                            Create New Post
                        </a>
                    </div>
                @endif

                {{-- GENERAL CARD --}}
                <div class="border border-gray-200 rounded-2xl p-6 bg-gray-50">
                    <h4 class="text-gray-500 font-bold uppercase text-xs mb-2">Navigation</h4>
                    <p class="text-sm text-gray-600 mb-4">View the public blog feed and latest categories.</p>
                    <a href="{{ route('home') }}" class="block text-center bg-white border border-gray-300 text-gray-700 py-2 rounded-lg font-semibold hover:bg-gray-100 transition">
                        Go to Blog Home
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
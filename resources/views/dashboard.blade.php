@extends('layouts.app')

@section('content')
<div class="py-12 bg-gray-50/50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Unified Header --}}
        <div class="mb-10">
            <h1 class="text-4xl font-black text-gray-900 tracking-tight">Dashboard</h1>
            <p class="text-gray-500 font-medium italic">
                Logged in as <span class="text-[#4B0082] font-black uppercase text-xs tracking-widest">{{ auth()->user()->role }}</span>
            </p>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm font-bold rounded-2xl flex items-center">
                {{ session('success') }}
            </div>
        @endif

        {{-- The Magic: Switch between roles --}}
        @if(auth()->user()->isAdmin())
            @include('dashboard.partials.admin')
        @elseif(auth()->user()->role === 'author')
            @include('dashboard.partials.author')
        @else
            @include('dashboard.partials.reader')
        @endif

    </div>
</div>
@endsection
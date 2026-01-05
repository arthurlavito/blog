<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Anim24')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans text-gray-900">

<div class="min-h-full flex flex-col">

    {{-- NAVBAR --}}
    <nav class="bg-white shadow sticky top-0 z-50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">

                {{-- Logo & Nav Links --}}
                <div class="flex items-center gap-8">
                    <a href="{{ route('posts.index') }}" class="flex items-center gap-2">
                        <img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=500"
                             class="h-8 w-8" alt="Logo">
                        <span class="font-bold text-[#4B0082] text-lg">Anim24</span>
                    </a>

                    <div class="hidden md:flex space-x-4">
                        <a href="{{ route('posts.index') }}"
                           class="px-3 py-2 rounded-md text-sm font-medium
                            {{ request()->routeIs('posts.*') 
                                ? 'bg-[#4B0082] text-white' 
                                : 'text-gray-600 hover:bg-gray-100 hover:text-[#4B0082]' }}">
                            Blog Posts
                        </a>
                        <a href="#"
                           class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-[#4B0082]">
                            Categories
                        </a>
                    </div>
                </div>

                {{-- User --}}
                <div class="hidden md:flex items-center gap-4">
                    <span class="text-gray-600 text-sm">{{ auth()->user()->name ?? 'Guest' }}</span>
                    <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e"
                         class="h-8 w-8 rounded-full object-cover" alt="User">
                </div>

                {{-- Mobile menu button --}}
                <div class="md:hidden flex items-center">
                    <button type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-[#4B0082] hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-[#4B0082]">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>

            </div>
        </div>
    </nav>

    {{-- HEADER --}}
    <header class="bg-gradient-to-r from-[#4B0082]/80 to-indigo-700 shadow-sm">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-extrabold text-white tracking-tight">
                @yield('header', 'Welcome to Anim24')
            </h1>
            <p class="mt-2 text-lg text-indigo-200">
                Your source for quality news and articles.
            </p>
        </div>
    </header>

    {{-- MAIN CONTENT --}}
    <main class="flex-1 bg-gray-50 py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            {{-- Flash messages --}}
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-500 text-white rounded shadow">
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    {{-- FOOTER --}}
    <footer class="bg-white border-t border-gray-200 mt-8">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 text-center text-gray-500 text-sm">
            &copy; {{ date('Y') }} Anim24. All rights reserved.
        </div>
    </footer>

</div>

</body>
</html>

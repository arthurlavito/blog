<nav class="bg-white shadow-md border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">

            {{-- Left: Logo + Links --}}
            <div class="flex items-center gap-6">

                {{-- Logo --}}
                <a href="{{ route('posts.index') }}" class="flex items-center gap-3">
                    <img 
                        src="{{ asset('images/logo.png') }}" 
                        alt="Anim24 Logo" 
                        class="h-28 w-auto"
                    >
                </a>

                {{-- Desktop Links --}}
                <div class="hidden md:flex items-center gap-2">
                    <a href="{{ route('posts.index') }}"
                       class="px-3 py-2 rounded-md text-sm font-medium 
                       {{ request()->routeIs('posts.*') 
                            ? 'bg-indigo-600 text-white' 
                            : 'text-gray-700 hover:bg-gray-100' }}">
                        Blog
                    </a>

                    @auth
                        <a href="{{ route('posts.create') }}"
                           class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-100">
                            Create Post
                        </a>
                    @endauth
                </div>
            </div>

            {{-- Right: Auth --}}
            <div class="flex items-center gap-4">
                @guest
                    <a href="{{ route('login') }}"
                       class="text-sm font-medium text-gray-700 hover:text-indigo-600">
                        Login
                    </a>
                    <a href="{{ route('register') }}"
                       class="px-4 py-2 rounded-md text-sm font-medium bg-indigo-600 text-white hover:bg-indigo-700">
                        Register
                    </a>
                @else
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                                class="flex items-center gap-2 focus:outline-none">
                            <img
                                class="h-8 w-8 rounded-full"
                                src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4B0082&color=fff"
                                alt="User avatar"
                            >
                            <span class="text-sm text-gray-700 font-medium">
                                {{ Auth::user()->name }}
                            </span>
                        </button>

                        {{-- Dropdown --}}
                        <div x-show="open" @click.away="open = false"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border z-50">
                            <a href="{{ route('dashboard') }}"
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Dashboard
                            </a>
                            <a href="{{ route('profile.edit') }}"
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Profile
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @endguest
            </div>

        </div>
    </div>
</nav>

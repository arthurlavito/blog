<nav class="bg-white shadow-md border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">

            {{-- Left Side: Brand & Main Nav --}}
            <div class="flex items-center gap-8">
                <a href="{{ route('home') }}" class="group">
                    <h1 class="text-4xl font-black tracking-tighter text-[#4B0082] flex items-baseline">
                        ANIM<span class="text-indigo-500">24</span>
                        <span class="text-xs font-bold text-gray-400 ml-1 group-hover:text-indigo-400 transition-colors">.COM</span>
                    </h1>
                </a>

                <div class="hidden md:flex items-center gap-4">
                    <a href="{{ route('posts.index') }}"
                       class="px-3 py-2 rounded-lg text-sm font-bold transition {{ request()->routeIs('posts.index') ? 'bg-indigo-50 text-[#4B0082]' : 'text-gray-600 hover:bg-gray-50' }}">
                        Explore News
                    </a>
                </div>
            </div>

            {{-- Right Side: Auth & Admin --}}
            <div class="flex items-center gap-4">
                @guest
                    <a href="{{ route('login') }}" class="text-sm font-bold text-gray-700 hover:text-[#4B0082]">Login</a>
                    <a href="{{ route('register') }}" class="px-5 py-2 rounded-full text-sm font-black bg-[#4B0082] text-white hover:shadow-lg transition">Join Us</a>
                @else
                    @if(auth()->user()->isAdmin() || auth()->user()->isAuthor())
                        <a href="{{ route('posts.create') }}" class="hidden sm:block px-4 py-2 rounded-lg text-xs font-black uppercase tracking-widest bg-emerald-500 text-white hover:bg-emerald-600 transition">
                            + Create Post
                        </a>
                    @endif

                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 focus:outline-none">
                            <div class="relative">
                                <img class="h-9 w-9 rounded-full border-2 {{ auth()->user()->isAdmin() ? 'border-indigo-500' : 'border-gray-200' }}" 
                                     src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=4B0082&color=fff&bold=true">
                                
                                @if(auth()->user()->isAdmin() && ($pendingAuthorCount ?? 0) > 0)
                                    <span class="absolute -top-1 -right-1 flex h-4 w-4">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500 text-[10px] text-white items-center justify-center font-bold">{{ $pendingAuthorCount }}</span>
                                    </span>
                                @endif
                            </div>
                        </button>

                        {{-- Dropdown Menu --}}
                        <div x-show="open" @click.away="open = false" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="absolute right-0 mt-3 w-56 bg-white rounded-2xl shadow-2xl border border-gray-100 py-2 z-50">
                            <div class="px-4 py-3 border-b border-gray-50">
                                <p class="text-xs font-black text-[#4B0082] uppercase tracking-widest">{{ auth()->user()->role }}</p>
                                <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
                            </div>
                            
                            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 font-medium">Dashboard</a>
                            
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.users.index') }}" class="block px-4 py-2 text-sm text-indigo-600 hover:bg-indigo-50 font-bold">
                                    Manage Users ({{ $pendingAuthorCount ?? 0 }})
                                </a>
                            @endif

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-bold">Logout</button>
                            </form>
                        </div>
                    </div>
                @endguest
            </div>
        </div>
    </div>
</nav>
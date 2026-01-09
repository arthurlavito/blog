{{-- Stats Row --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
    <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Total Users</p>
        <p class="text-3xl font-black text-[#4B0082]">{{ \App\Models\User::count() }}</p>
    </div>
    <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Total Articles</p>
        <p class="text-3xl font-black text-indigo-500">{{ \App\Models\Post::count() }}</p>
    </div>
    <div class="bg-emerald-500 p-8 rounded-[2.5rem] shadow-xl shadow-emerald-100 text-white">
        <p class="text-[10px] font-black uppercase tracking-widest opacity-70 mb-1">Active Authors</p>
        <p class="text-3xl font-black">{{ \App\Models\User::where('role', 'author')->count() }}</p>
    </div>
    {{-- NEW: Featured Status Stat --}}
    <div class="bg-amber-400 p-8 rounded-[2.5rem] shadow-xl shadow-amber-100 text-white">
        <p class="text-[10px] font-black uppercase tracking-widest opacity-70 mb-1">Live Feature</p>
        <p class="text-xs font-bold truncate">
            {{ \App\Models\Post::where('is_featured', true)->first()->title ?? 'None Set' }}
        </p>
    </div>
</div>

{{-- Quick Management Section --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    
    {{-- Left Column: Urgent Approvals --}}
    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden flex flex-col">
        <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center">
            <h3 class="font-black text-gray-800 uppercase tracking-widest text-xs">Pending Applications</h3>
            <a href="{{ route('admin.users.index') }}" class="text-[10px] font-black text-[#4B0082] uppercase hover:underline">View All</a>
        </div>
        
        <div class="p-4 flex-grow">
            @php 
                $pending = \App\Models\User::whereNotNull('author_requested_at')
                            ->where('role', 'reader')
                            ->limit(3)
                            ->get();
            @endphp

            @forelse($pending as $user)
                <div class="flex items-center justify-between p-4 hover:bg-gray-50 rounded-2xl transition">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-[#4B0082] font-bold text-xs">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <span class="text-sm font-bold text-gray-700">{{ $user->name }}</span>
                    </div>
                    <form action="{{ route('admin.author.approve', $user) }}" method="POST">
                        @csrf
                        <button class="text-[10px] font-black uppercase text-emerald-500 hover:text-emerald-600">Approve</button>
                    </form>
                </div>
            @empty
                <p class="text-center py-6 text-xs text-gray-400 italic">No pending applications</p>
            @endforelse
        </div>
    </div>

    {{-- Right Column: Recent Site Content --}}
    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center">
            <h3 class="font-black text-gray-800 uppercase tracking-widest text-xs">Manage Posts & Features</h3>
            <a href="{{ route('posts.create') }}" class="text-[10px] font-black text-emerald-500 uppercase hover:underline">+ New Post</a>
        </div>
        {{-- This table now includes the Feature toggle --}}
        @include('dashboard.partials._posts_table')
    </div>

    {{-- Bottom Column: Category Management --}}
    <div class="lg:col-span-2 bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center">
            <h3 class="font-black text-gray-800 uppercase tracking-widest text-xs">Manage Content Categories</h3>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ \App\Models\Category::count() }} Total</span>
        </div>
        
        <div class="p-8">
            <form action="{{ route('categories.store') }}" method="POST" class="flex flex-col md:flex-row gap-4 mb-8">
                @csrf
                <div class="flex-1">
                    <input type="text" name="name" placeholder="Enter new category name..." 
                           class="w-full bg-gray-50 border-none rounded-2xl px-6 py-3 text-sm focus:ring-2 focus:ring-[#4B0082] transition"
                           required>
                </div>
                <button type="submit" class="bg-[#4B0082] text-white px-10 py-3 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-indigo-900 transition shadow-lg shadow-indigo-100">
                    Create Category
                </button>
            </form>

            <div class="flex flex-wrap gap-3">
                @forelse(\App\Models\Category::all() as $category)
                    <div class="group flex items-center gap-2 px-4 py-2 bg-indigo-50/50 border border-indigo-100 rounded-xl transition-all hover:bg-white hover:shadow-md">
                        <span class="text-[11px] font-black text-[#4B0082] uppercase tracking-widest">
                            {{ $category->name }}
                        </span>
                        <span class="text-[9px] font-bold text-indigo-300 bg-white px-1.5 py-0.5 rounded-md border border-indigo-50">
                            {{ $category->posts()->count() }}
                        </span>
                    </div>
                @empty
                    <div class="w-full py-4 text-center border-2 border-dashed border-gray-50 rounded-2xl">
                        <p class="text-xs text-gray-400 font-medium">No categories created yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
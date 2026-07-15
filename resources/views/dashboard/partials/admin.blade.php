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

{{-- Pending Review Queue --}}
@php $pendingPosts = \App\Models\Post::with('user')->pending()->latest()->get(); @endphp
@if($pendingPosts->isNotEmpty())
<div class="bg-amber-50 border border-amber-100 rounded-[2.5rem] p-8 mb-8">
    <h3 class="font-black text-amber-800 uppercase tracking-widest text-xs mb-4">
        Pending Review — {{ $pendingPosts->count() }} {{ $pendingPosts->count() === 1 ? 'Post' : 'Posts' }}
    </h3>
    <div class="space-y-3">
        @foreach($pendingPosts as $pending)
            <div class="flex items-center justify-between bg-white rounded-2xl px-5 py-3 border border-amber-100">
                <div>
                    <p class="text-sm font-bold text-gray-800">{{ $pending->title }}</p>
                    <p class="text-[10px] text-gray-400 mt-0.5">by {{ $pending->user->name ?? '—' }} · {{ $pending->updated_at->diffForHumans() }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <form action="{{ route('admin.posts.publish', $pending) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-1.5 text-[9px] font-black uppercase rounded-lg bg-emerald-100 text-emerald-600 border border-emerald-200 hover:bg-emerald-500 hover:text-white transition-all">
                            Publish
                        </button>
                    </form>
                    <form action="{{ route('admin.posts.reject', $pending) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-1.5 text-[9px] font-black uppercase rounded-lg bg-rose-50 text-rose-500 border border-rose-100 hover:bg-rose-500 hover:text-white transition-all">
                            Reject
                        </button>
                    </form>
                    <a href="{{ route('posts.edit', $pending) }}" class="px-4 py-1.5 text-[9px] font-black uppercase rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-100 hover:bg-indigo-600 hover:text-white transition-all">
                        Review
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

{{-- Pending Comments Queue --}}
@php $pendingComments = \App\Models\Comment::with(['user', 'post'])->pending()->latest()->take(10)->get(); @endphp
@if($pendingComments->isNotEmpty())
<div class="bg-blue-50 border border-blue-100 rounded-[2.5rem] p-8 mb-8">
    <h3 class="font-black text-blue-800 uppercase tracking-widest text-xs mb-4">
        Comments Awaiting Moderation — {{ $pendingComments->count() }}
    </h3>
    <div class="space-y-3">
        @foreach($pendingComments as $comment)
            <div class="bg-white rounded-2xl px-5 py-4 border border-blue-100">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0 flex-1">
                        <p class="text-[10px] text-gray-400 font-bold mb-1">
                            <span class="text-gray-700 font-black">{{ $comment->user->name ?? '—' }}</span>
                            on <a href="{{ route('posts.show', $comment->post) }}" class="text-indigo-500 hover:underline">{{ $comment->post->title ?? '—' }}</a>
                            · {{ $comment->created_at->diffForHumans() }}
                        </p>
                        <p class="text-sm text-gray-700 line-clamp-2">{{ $comment->body }}</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <form action="{{ route('admin.comments.approve', $comment) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 text-[9px] font-black uppercase rounded-lg bg-emerald-100 text-emerald-600 border border-emerald-200 hover:bg-emerald-500 hover:text-white transition-all">
                                Approve
                            </button>
                        </form>
                        <form action="{{ route('admin.comments.spam', $comment) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 text-[9px] font-black uppercase rounded-lg bg-orange-50 text-orange-500 border border-orange-100 hover:bg-orange-500 hover:text-white transition-all">
                                Spam
                            </button>
                        </form>
                        <form action="{{ route('admin.comments.trash', $comment) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 text-[9px] font-black uppercase rounded-lg bg-rose-50 text-rose-500 border border-rose-100 hover:bg-rose-500 hover:text-white transition-all">
                                Trash
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

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
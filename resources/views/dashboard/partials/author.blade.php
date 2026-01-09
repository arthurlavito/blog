<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
    <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Your Published Posts</p>
        <p class="text-3xl font-black text-[#4B0082]">{{ auth()->user()->posts()->count() }}</p>
    </div>
    <a href="{{ route('posts.create') }}" class="bg-emerald-500 p-8 rounded-[2.5rem] shadow-xl shadow-emerald-100 text-white hover:scale-[1.02] transition-transform">
        <p class="text-[10px] font-black uppercase tracking-widest opacity-70 mb-1">Quick Action</p>
        <p class="text-xl font-black">+ Create New Article</p>
    </a>
</div>

@include('dashboard.partials._posts_table')
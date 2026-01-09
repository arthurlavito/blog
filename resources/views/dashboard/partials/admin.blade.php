{{-- Stats Row --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
    {{-- ... (Keep the stats cards we built) ... --}}
</div>

{{-- Quick Management Section --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    
    {{-- Left: Urgent Approvals --}}
    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center">
            <h3 class="font-black text-gray-800 uppercase tracking-widest text-xs">Pending Applications</h3>
            <a href="{{ route('admin.users.index') }}" class="text-[10px] font-black text-[#4B0082] uppercase hover:underline">View All</a>
        </div>
        
        <div class="p-4">
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

    {{-- Right: Recent Site Content --}}
    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-50">
            <h3 class="font-black text-gray-800 uppercase tracking-widest text-xs">Your Recent Posts</h3>
        </div>
        @include('dashboard.partials._posts_table')
    </div>
</div>
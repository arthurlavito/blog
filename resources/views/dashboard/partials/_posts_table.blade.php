<div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-[10px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-50">
                    <th class="px-8 py-4">Article Details</th>
                    <th class="px-8 py-4 text-right">Management Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                {{-- Eager loading 'category' and 'user' for optimal performance --}}
                @forelse(\App\Models\Post::with(['category', 'user'])->latest()->get() as $post)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                {{-- Mini Thumbnail Preview --}}
                                @if($post->image)
                                    <img src="{{ asset('storage/' . $post->image) }}" class="w-10 h-10 rounded-xl object-cover border border-gray-100 shadow-sm">
                                @else
                                    <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-[10px] font-black text-indigo-300">A24</div>
                                @endif
                                
                                <div class="truncate max-w-xs">
                                    <p class="text-sm font-bold text-gray-800 group-hover:text-[#4B0082] transition-colors truncate">
                                        {{ $post->title }}
                                    </p>
                                    <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest mt-0.5">
                                        <span class="text-indigo-500">{{ $post->category->name ?? 'Uncategorized' }}</span> 
                                        <span class="mx-1 text-gray-200">•</span> 
                                        {{ $post->created_at->format('M d, Y') }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        <td class="px-8 py-5 text-right">
                            <div class="flex justify-end items-center gap-3">
                                {{-- Feature Toggle Button (Admin Only Function) --}}
                                <form action="{{ route('admin.posts.feature', $post) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                        class="px-3 py-1.5 text-[9px] font-black uppercase rounded-lg transition-all 
                                        {{ $post->is_featured 
                                            ? 'bg-amber-100 text-amber-600 border border-amber-200' 
                                            : 'bg-gray-50 text-gray-400 border border-gray-100 hover:border-amber-300 hover:text-amber-500' }}">
                                        {{ $post->is_featured ? '★ Featured' : '☆ Feature' }}
                                    </button>
                                </form>

                                {{-- Edit Button --}}
                                <a href="{{ route('posts.edit', $post) }}" class="px-4 py-2 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-600 hover:text-white transition-all">
                                    Edit
                                </a>

                                {{-- Delete Form --}}
                                <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Are you sure? This will permanently delete this article.')">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="submit" class="text-[10px] font-black uppercase text-rose-400 hover:text-rose-600 transition">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-8 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-300 mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <p class="text-sm font-bold text-gray-500">No articles found.</p>
                                <p class="text-xs text-gray-400 mt-1">Start by creating your first news story.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
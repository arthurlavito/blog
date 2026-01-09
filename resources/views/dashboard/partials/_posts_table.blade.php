<div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-left border-collapse">
        <tbody class="divide-y divide-gray-50">
            @forelse(\App\Models\Post::where('user_id', auth()->id())->latest()->get() as $post)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-8 py-5">
                        <p class="text-sm font-bold text-gray-800">{{ $post->title }}</p>
                        <p class="text-[10px] text-gray-400 uppercase font-black tracking-widest">{{ $post->category }} • {{ $post->created_at->diffForHumans() }}</p>
                    </td>
                    <td class="px-8 py-5 text-right">
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('posts.edit', $post) }}" class="text-xs font-black uppercase text-indigo-500 hover:text-indigo-700">Edit</a>
                            <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Delete image and post?')">
                                @csrf @method('DELETE')
                                <button class="text-xs font-black uppercase text-rose-500">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td class="px-8 py-10 text-center text-gray-400 italic">No posts found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
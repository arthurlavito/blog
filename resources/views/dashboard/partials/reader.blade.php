<div class="space-y-8">
    {{-- Welcome Card --}}
    <div class="bg-gradient-to-br from-[#4B0082] to-indigo-900 rounded-[2.5rem] p-8 md:p-12 text-white shadow-xl shadow-indigo-100 relative overflow-hidden">
        <div class="relative z-10">
            <h3 class="text-2xl md:text-3xl font-black mb-2">Welcome to the Inner Circle, {{ explode(' ', auth()->user()->name)[0] }}!</h3>
            <p class="text-indigo-100 max-w-xl text-sm md:text-base leading-relaxed">
                As a Reader, you're the heart of Anim24. You can explore the latest news, join discussions, and save your favorite stories.
            </p>
        </div>
        {{-- Decorative element --}}
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Application Section --}}
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    <div>
                        <h4 class="font-black text-gray-900 uppercase tracking-tight">Become a Creator</h4>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Upgrade your account</p>
                    </div>
                </div>

                <p class="text-gray-600 text-sm mb-8 leading-relaxed">
                    Have a scoop or an opinion on the latest anime? Apply for <strong>Author Status</strong> to start publishing your own articles directly to the Anim24 front page.
                </p>

                @if(auth()->user()->author_requested_at)
                    <div class="inline-flex items-center px-6 py-4 bg-amber-50 rounded-2xl border border-amber-100">
                        <span class="relative flex h-3 w-3 mr-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                        </span>
                        <p class="text-sm font-black text-amber-700 uppercase tracking-widest">Application Pending Review</p>
                    </div>
                    <p class="mt-4 text-[11px] text-gray-400 font-medium italic">Our admins usually respond within 24 hours.</p>
                @else
                    <form action="{{ route('author.request') }}" method="POST">
                        @csrf
                        <button type="submit" class="group bg-[#4B0082] text-white px-10 py-4 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-indigo-900 transition-all shadow-lg shadow-indigo-100 flex items-center gap-3">
                            Submit Application
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </button>
                    </form>
                @endif
            </div>

            {{-- Community Stats Placeholder --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white p-6 rounded-[2rem] border border-gray-50 shadow-sm text-center">
                    <p class="text-[10px] font-black uppercase text-gray-400 tracking-widest mb-1">Total Likes</p>
                    <p class="text-2xl font-black text-gray-800">0</p>
                </div>
                <div class="bg-white p-6 rounded-[2rem] border border-gray-50 shadow-sm text-center">
                    <p class="text-[10px] font-black uppercase text-gray-400 tracking-widest mb-1">Comments</p>
                    <p class="text-2xl font-black text-gray-800">0</p>
                </div>
            </div>
        </div>

        {{-- Sidebar for Reader --}}
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm">
                <h4 class="text-xs font-black uppercase text-gray-400 tracking-widest mb-4">Trending Now</h4>
                <ul class="space-y-4">
                    @foreach(\App\Models\Post::latest()->take(3)->get() as $trending)
                        <li>
                            <a href="{{ route('posts.show', $trending) }}" class="group block">
                                <span class="text-xs font-bold text-gray-800 group-hover:text-[#4B0082] transition-colors leading-tight line-clamp-2">
                                    {{ $trending->title }}
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
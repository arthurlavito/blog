<x-guest-layout>
    <div class="text-center">
        <div class="w-16 h-16 bg-indigo-50 text-[#4B0082] rounded-2xl flex items-center justify-center mx-auto mb-6">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
        </div>
        
        <h2 class="text-xl font-black text-[#4B0082] mb-2">Check Your Inbox</h2>
        <p class="text-sm text-gray-500 leading-relaxed mb-8">
            {{ __('Before getting started, please verify your email address by clicking the link we just sent you.') }}
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 p-4 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-xl border border-emerald-100">
            {{ __('A new verification link has been sent!') }}
        </div>
    @endif

    <div class="flex flex-col gap-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="w-full bg-[#4B0082] text-white py-3 rounded-xl font-bold hover:opacity-90 transition">
                {{ __('Resend Email') }}
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-gray-400 font-bold text-xs uppercase tracking-widest hover:text-red-500 transition">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
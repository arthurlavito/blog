<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-8 text-center">
        <h2 class="text-2xl font-black text-gray-800">Welcome Back</h2>
        <p class="text-sm text-gray-500 font-medium mt-1">Sign in to your account</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <div>
            <label class="block text-xs font-black uppercase text-gray-400 mb-2 tracking-widest">Email Address</label>
            <input type="email" name="email" :value="old('email')" required autofocus class="w-full bg-gray-50 border-none rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-[#4B0082] transition shadow-sm" placeholder="name@example.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <div class="flex justify-between items-center mb-2">
                <label class="block text-xs font-black uppercase text-gray-400 tracking-widest">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-[10px] font-black uppercase text-indigo-500 hover:text-[#4B0082]">Forgot?</a>
                @endif
            </div>
            <input type="password" name="password" required class="w-full bg-gray-50 border-none rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-[#4B0082] transition shadow-sm" placeholder="••••••••">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center">
            <input id="remember_me" type="checkbox" class="rounded-lg border-gray-200 text-[#4B0082] focus:ring-[#4B0082]" name="remember">
            <span class="ms-2 text-sm font-bold text-gray-500">{{ __('Stay signed in') }}</span>
        </div>

        <button type="submit" class="w-full bg-[#4B0082] text-white py-4 rounded-2xl font-black text-sm hover:opacity-90 transition-all shadow-lg shadow-indigo-100 uppercase tracking-widest">
            Log In
        </button>

        <p class="text-center text-sm font-bold text-gray-500 mt-6">
            New here? <a href="{{ route('register') }}" class="text-indigo-600 hover:text-[#4B0082]">Join the community</a>
        </p>
    </form>
</x-guest-layout>
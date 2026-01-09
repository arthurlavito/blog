<x-guest-layout>
    <h2 class="text-2xl font-black text-[#4B0082] mb-1">Set New Password</h2>
    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-8">Secure your access</p>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <label class="block text-xs font-black uppercase text-gray-500 mb-1">Email</label>
            <input id="email" type="email" name="email" class="w-full bg-gray-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#4B0082]" value="{{ old('email', $request->email) }}" required />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div>
            <label class="block text-xs font-black uppercase text-gray-500 mb-1">New Password</label>
            <input id="password" type="password" name="password" class="w-full bg-gray-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#4B0082]" required />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div>
            <label class="block text-xs font-black uppercase text-gray-500 mb-1">Confirm New Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="w-full bg-gray-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#4B0082]" required />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <button type="submit" class="w-full bg-[#4B0082] text-white py-4 rounded-2xl font-bold hover:opacity-90 transition shadow-lg">
            {{ __('Reset Password') }}
        </button>
    </form>
</x-guest-layout>
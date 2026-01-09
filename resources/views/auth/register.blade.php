<x-guest-layout>
    <div class="mb-8 text-center">
        <h2 class="text-2xl font-black text-gray-800">Create Account</h2>
        <p class="text-sm text-gray-500 font-medium mt-1">Start your journey with Anim24</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <label class="block text-xs font-black uppercase text-gray-400 mb-2 tracking-widest">Full Name</label>
            <input type="text" name="name" :value="old('name')" required autofocus class="w-full bg-gray-50 border-none rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-[#4B0082] transition shadow-sm" placeholder="John Doe">
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <label class="block text-xs font-black uppercase text-gray-400 mb-2 tracking-widest">Email</label>
            <input type="email" name="email" :value="old('email')" required class="w-full bg-gray-50 border-none rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-[#4B0082] transition shadow-sm" placeholder="email@address.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-xs font-black uppercase text-gray-400 mb-2 tracking-widest">Password</label>
                <input type="password" name="password" required class="w-full bg-gray-50 border-none rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-[#4B0082] transition shadow-sm">
            </div>
            <div>
                <label class="block text-xs font-black uppercase text-gray-400 mb-2 tracking-widest">Confirm</label>
                <input type="password" name="password_confirmation" required class="w-full bg-gray-50 border-none rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-[#4B0082] transition shadow-sm">
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <button type="submit" class="w-full bg-[#4B0082] text-white py-4 rounded-2xl font-black text-sm hover:opacity-90 transition-all shadow-lg shadow-indigo-100 uppercase tracking-widest mt-4">
            Create My Account
        </button>

        <p class="text-center text-sm font-bold text-gray-500 mt-6">
            Already a member? <a href="{{ route('login') }}" class="text-indigo-600 hover:text-[#4B0082]">Log in instead</a>
        </p>
    </form>
</x-guest-layout>
<section>
    <header class="mb-8">
        <h2 class="text-xl font-black text-gray-800 flex items-center">
            <span class="w-1.5 h-5 bg-indigo-500 rounded-full mr-3"></span>
            {{ __('Update Password') }}
        </h2>
        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-2">
            {{ __('Ensure your account is using a long, random password.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-6 max-w-xl">
        @csrf
        @method('put')

        <div>
            <label class="block text-xs font-black uppercase text-gray-500 mb-2">{{ __('Current Password') }}</label>
            <input id="update_password_current_password" name="current_password" type="password" class="w-full bg-gray-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#4B0082] transition shadow-sm" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <label class="block text-xs font-black uppercase text-gray-500 mb-2">{{ __('New Password') }}</label>
            <input id="update_password_password" name="password" type="password" class="w-full bg-gray-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#4B0082] transition shadow-sm" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <label class="block text-xs font-black uppercase text-gray-500 mb-2">{{ __('Confirm Password') }}</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="w-full bg-gray-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#4B0082] transition shadow-sm" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="bg-[#4B0082] text-white px-8 py-3 rounded-xl font-bold text-sm hover:opacity-90 transition shadow-lg shadow-indigo-100">
                {{ __('Update Security') }}
            </button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-xs font-black uppercase text-emerald-500 tracking-widest">
                    {{ __('Password Saved') }}
                </p>
            @endif
        </div>
    </form>
</section>
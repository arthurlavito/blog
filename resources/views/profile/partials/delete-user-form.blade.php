<section class="space-y-6">
    <header>
        <h2 class="text-xl font-black text-rose-600 flex items-center">
            <span class="w-1.5 h-5 bg-rose-500 rounded-full mr-3"></span>
            {{ __('Danger Zone') }}
        </h2>
        <p class="text-xs text-rose-400 font-bold uppercase tracking-widest mt-2">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted.') }}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="rounded-xl px-6 py-3 font-bold uppercase tracking-widest text-xs"
    >{{ __('Delete Account') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-8">
            @csrf
            @method('delete')

            <h2 class="text-xl font-black text-gray-800">
                {{ __('Are you sure?') }}
            </h2>

            <p class="mt-4 text-sm text-gray-500 font-medium">
                {{ __('Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="mt-6">
                <label class="sr-only">{{ __('Password') }}</label>
                <input id="password" name="password" type="password" class="w-full bg-gray-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-rose-500 transition" placeholder="{{ __('Your Password') }}" />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="px-6 py-3 text-xs font-black uppercase text-gray-400 hover:text-gray-600 transition">
                    {{ __('Cancel') }}
                </button>

                <button type="submit" class="bg-rose-600 text-white px-6 py-3 rounded-xl text-xs font-black uppercase hover:bg-rose-700 transition shadow-lg shadow-rose-100">
                    {{ __('Permanently Delete') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
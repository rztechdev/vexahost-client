<section class="space-y-6">
    <header>
        <h2 class="text-lg font-bold text-rose-600 dark:text-rose-400 tracking-tight flex items-center gap-2">
            <span class="material-symbols-outlined">warning</span>
            {{ __('Hapus Akun') }}
        </h2>

        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
            {{ __('Setelah akun Anda dihapus, semua sumber daya dan data di dalamnya akan dihapus secara permanen. Harap unduh data atau informasi apa pun yang ingin Anda simpan.') }}
        </p>
    </header>

    <button x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="inline-flex justify-center items-center py-2.5 px-5 border border-transparent rounded-xl shadow-md shadow-rose-600/10 text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 focus:outline-none transition-all">
        {{ __('Hapus Akun Permanen') }}
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl">
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold text-zinc-900 dark:text-white tracking-tight">
                {{ __('Apakah Anda yakin ingin menghapus akun Anda?') }}
            </h2>

            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                {{ __('Tindakan ini tidak dapat dibatalkan. Masukkan kata sandi Anda untuk mengonfirmasi penghapusan akun secara permanen.') }}
            </p>

            <div class="mt-6">
                <label for="password" class="sr-only">Password</label>
                <input id="password" name="password" type="password" placeholder="{{ __('Masukkan Kata Sandi') }}"
                       class="mt-1 block w-full sm:w-3/4 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-zinc-50 placeholder-zinc-400 dark:placeholder-zinc-500 focus:border-rose-600 dark:focus:border-rose-400 focus:ring focus:ring-rose-600/20 dark:focus:ring-rose-500/20 transition-all px-4 py-2.5 text-sm">
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-rose-600 dark:text-rose-400 font-medium" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" 
                        class="inline-flex justify-center items-center py-2.5 px-5 border border-zinc-200 dark:border-zinc-800 rounded-xl text-sm font-bold text-zinc-700 dark:text-zinc-300 bg-zinc-50 dark:bg-zinc-950 hover:bg-zinc-100 dark:hover:bg-zinc-900 focus:outline-none transition-all">
                    {{ __('Batal') }}
                </button>

                <button type="submit" 
                        class="inline-flex justify-center items-center py-2.5 px-5 border border-transparent rounded-xl shadow-md shadow-rose-600/10 text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 focus:outline-none transition-all">
                    {{ __('Ya, Hapus Akun') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
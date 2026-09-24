<section>
    <header>
        <h2 class="text-lg font-bold text-zinc-900 dark:text-white tracking-tight">
            {{ __('Perbarui Kata Sandi') }}
        </h2>

        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
            {{ __('Pastikan akun Anda menggunakan kata sandi yang panjang dan acak agar tetap aman.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300">Kata Sandi Saat Ini</label>
            <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password"
                   class="mt-1.5 block w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-zinc-50 placeholder-zinc-400 dark:placeholder-zinc-500 focus:border-emerald-600 dark:focus:border-emerald-400 focus:ring focus:ring-emerald-500/20 dark:focus:ring-emerald-400/20 transition-all px-4 py-2.5 text-sm">
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-rose-600 dark:text-rose-400 font-medium" />
        </div>

        <div>
            <label for="update_password_password" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300">Kata Sandi Baru</label>
            <input id="update_password_password" name="password" type="password" autocomplete="new-password"
                   class="mt-1.5 block w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-zinc-50 placeholder-zinc-400 dark:placeholder-zinc-500 focus:border-emerald-600 dark:focus:border-emerald-400 focus:ring focus:ring-emerald-500/20 dark:focus:ring-emerald-400/20 transition-all px-4 py-2.5 text-sm">
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-rose-600 dark:text-rose-400 font-medium" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300">Konfirmasi Kata Sandi</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                   class="mt-1.5 block w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-zinc-50 placeholder-zinc-400 dark:placeholder-zinc-500 focus:border-emerald-600 dark:focus:border-emerald-400 focus:ring focus:ring-emerald-500/20 dark:focus:ring-emerald-400/20 transition-all px-4 py-2.5 text-sm">
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-rose-600 dark:text-rose-400 font-medium" />
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="inline-flex justify-center items-center py-2.5 px-5 border border-transparent rounded-xl shadow-md shadow-emerald-600/10 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600 dark:focus:ring-emerald-400 focus:ring-offset-zinc-50 dark:focus:ring-offset-zinc-950 transition-all">
                {{ __('Perbarui Sandi') }}
            </button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }"
                   x-show="show"
                   x-transition
                   x-init="setTimeout(() => show = false, 2500)"
                   class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                   <span class="material-symbols-outlined text-[18px]">check_circle</span>
                   {{ __('Berhasil diperbarui.') }}
                </p>
            @endif
        </div>
    </form>
</section>
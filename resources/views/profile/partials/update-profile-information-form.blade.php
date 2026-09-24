<section>
    <header>
        <h2 class="text-lg font-bold text-zinc-900 dark:text-white tracking-tight">
            {{ __('Informasi Profil') }}
        </h2>

        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
            {{ __("Perbarui informasi profil dan alamat email akun Anda.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300">Nama Lengkap</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name"
                   class="mt-1.5 block w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-zinc-50 placeholder-zinc-400 dark:placeholder-zinc-500 focus:border-emerald-600 dark:focus:border-emerald-400 focus:ring focus:ring-emerald-500/20 dark:focus:ring-emerald-400/20 transition-all px-4 py-2.5 text-sm">
            <x-input-error class="mt-2 text-rose-600 dark:text-rose-400 font-medium" :messages="$errors->get('name')" />
        </div>

        <div>
            <label for="email" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300">Email Address</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                   class="mt-1.5 block w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-zinc-50 placeholder-zinc-400 dark:placeholder-zinc-500 focus:border-emerald-600 dark:focus:border-emerald-400 focus:ring focus:ring-emerald-500/20 dark:focus:ring-emerald-400/20 transition-all px-4 py-2.5 text-sm">
            <x-input-error class="mt-2 text-rose-600 dark:text-rose-400 font-medium" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-3 text-zinc-500 dark:text-zinc-400">
                        {{ __('Alamat email Anda belum diverifikasi.') }}

                        <button form="send-verification" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-bold hover:underline focus:outline-none transition-colors">
                            {{ __('Klik di sini untuk mengirim ulang email verifikasi.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-semibold text-sm text-emerald-600 dark:text-emerald-400">
                            {{ __('Tautan verifikasi baru telah dikirim ke alamat email Anda.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="inline-flex justify-center items-center py-2.5 px-5 border border-transparent rounded-xl shadow-md shadow-emerald-600/10 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600 dark:focus:ring-emerald-400 focus:ring-offset-zinc-50 dark:focus:ring-offset-zinc-950 transition-all">
                {{ __('Simpan Perubahan') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }"
                   x-show="show"
                   x-transition
                   x-init="setTimeout(() => show = false, 2500)"
                   class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                   <span class="material-symbols-outlined text-[18px]">check_circle</span>
                   {{ __('Tersimpan.') }}
                </p>
            @endif
        </div>
    </form>
</section>

<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center items-center pt-6 sm:pt-0 bg-zinc-50 dark:bg-zinc-950 font-sans relative overflow-hidden transition-colors duration-300">
        
        <div class="absolute top-0 right-0 w-[400px] h-[400px] bg-emerald-600/5 rounded-full blur-[100px] pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-[300px] h-[300px] bg-emerald-600/5 rounded-full blur-[100px] pointer-events-none"></div>

        <div class="w-full sm:max-w-md mt-6 px-8 py-10 bg-white dark:bg-zinc-900 shadow-2xl border border-zinc-200 dark:border-zinc-800 sm:rounded-2xl relative z-10 transition-colors duration-300">
            
            <div class="flex flex-col items-center justify-center mb-8">
                <div class="w-12 h-12 bg-emerald-600 dark:bg-emerald-550 rounded-xl flex items-center justify-center text-white shadow-md shadow-emerald-600/10 mb-4">
                    <span class="material-symbols-outlined font-bold text-2xl">lock_reset</span>
                </div>
                <h2 class="text-2xl font-bold text-zinc-900 dark:text-white tracking-tight">Atur Ulang Sandi</h2>
                <p class="text-zinc-500 dark:text-zinc-400 text-sm mt-1 text-center">Buat kata sandi baru untuk mengamankan akun Anda</p>
            </div>

            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div>
                    <label for="email" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" 
                           class="mt-1.5 block w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-zinc-50 placeholder-zinc-400 dark:placeholder-zinc-500 focus:border-emerald-600 dark:focus:border-emerald-400 focus:ring focus:ring-emerald-650/20 dark:focus:ring-emerald-400/20 transition-all px-4 py-2.5 text-sm">
                    @error('email')
                        <p class="text-rose-600 dark:text-rose-455 text-xs mt-2 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-5">
                    <label for="password" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300">Password Baru</label>
                    <input id="password" type="password" name="password" required autocomplete="new-password" 
                           class="mt-1.5 block w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-zinc-50 placeholder-zinc-400 dark:placeholder-zinc-500 focus:border-emerald-600 dark:focus:border-emerald-400 focus:ring focus:ring-emerald-650/20 dark:focus:ring-emerald-400/20 transition-all px-4 py-2.5 text-sm">
                    @error('password')
                        <p class="text-rose-600 dark:text-rose-455 text-xs mt-2 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-5">
                    <label for="password_confirmation" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300">Konfirmasi Password Baru</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" 
                           class="mt-1.5 block w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-zinc-50 placeholder-zinc-400 dark:placeholder-zinc-500 focus:border-emerald-600 dark:focus:border-emerald-400 focus:ring focus:ring-emerald-650/20 dark:focus:ring-emerald-400/20 transition-all px-4 py-2.5 text-sm">
                    @error('password_confirmation')
                        <p class="text-rose-600 dark:text-rose-455 text-xs mt-2 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-8">
                    <button type="submit" class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl shadow-md shadow-emerald-600/10 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600 dark:focus:ring-emerald-400 focus:ring-offset-zinc-50 dark:focus:ring-offset-zinc-950 transition-all">
                        Simpan Sandi Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>

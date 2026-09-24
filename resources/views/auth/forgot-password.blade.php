<x-guest-layout>
    <!-- Floating Card Wrapper (coordinates shadow & hover lift) -->
    <div class="w-full sm:max-w-md relative group py-10 flex flex-col justify-center items-center">
        <!-- Realistic dynamic floor shadow -->
        <div class="absolute bottom-4 left-[10%] w-[80%] h-8 bg-zinc-950/20 dark:bg-black/60 rounded-full pointer-events-none transition-all duration-700 ease-out group-hover:opacity-10 group-hover:scale-x-75 group-hover:blur-[64px] animate-floor-shadow z-0"></div>

        <!-- Bobbing float animation container -->
        <div class="w-full relative z-10 animate-float">
            <!-- Interactive Card Container -->
            <div id="auth-card" class="w-full super-glass border border-zinc-200/60 dark:border-zinc-800/60 rounded-[2rem] px-8 py-10 overflow-hidden transition-all duration-700 ease-out group-hover:-translate-y-4 group-hover:shadow-[0_45px_85px_-20px_rgba(0,0,0,0.22),_0_20px_40px_-25px_rgba(0,0,0,0.15),_0_0_60px_0px_rgba(234,88,12,0.12)] dark:group-hover:shadow-[0_55px_100px_-25px_rgba(0,0,0,0.8),_0_35px_60px_-30px_rgba(0,0,0,0.7),_0_0_65px_0px_rgba(234,88,12,0.08)] shadow-2xl">
            
            <div class="flex flex-col items-center justify-center mb-6">
                <div class="w-12 h-12 bg-emerald-600 dark:bg-emerald-500 rounded-xl flex items-center justify-center text-white shadow-md shadow-emerald-600/10 mb-4">
                    <span class="material-symbols-outlined font-bold text-2xl">mail_lock</span>
                </div>
                <h2 class="text-2xl font-bold text-zinc-900 dark:text-white tracking-tight">Lupa Kata Sandi?</h2>
            </div>

            <div class="mb-6 text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed text-center font-medium">
                Tidak masalah. Cukup beri tahu kami alamat email Anda, dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus 
                           class="mt-1.5 block w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-zinc-50 placeholder-zinc-400 dark:placeholder-zinc-500 focus:border-emerald-600 dark:focus:border-emerald-400 focus:ring focus:ring-emerald-500/20 dark:focus:ring-emerald-400/20 transition-all px-4 py-2.5 text-sm">
                    @error('email')
                        <p class="text-rose-600 dark:text-rose-400 text-xs mt-2 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-8">
                    <button type="submit" class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl shadow-md shadow-emerald-600/10 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600 dark:focus:ring-emerald-400 focus:ring-offset-zinc-50 dark:focus:ring-offset-zinc-950 transition-all">
                        Kirim Tautan Reset Sandi
                    </button>
                </div>
                
                <div class="mt-6 text-center text-sm text-zinc-500 dark:text-zinc-400 font-medium">
                    Ingat kata sandi Anda? 
                    <a href="{{ route('login') }}" class="font-bold text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors">Kembali ke Log In</a>
                </div>
            </form>
        </div>
    </div>
</div>
</x-guest-layout>

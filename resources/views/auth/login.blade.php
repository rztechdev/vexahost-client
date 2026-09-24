<x-landing-layout>
    <!-- ================================================================= -->
    <!-- 1. FORM MASUK (Admin Panel & Client Panel)                          -->
    <!-- ================================================================= -->
    <section id="masuk" class="relative w-full px-4 sm:px-6 lg:px-8 pt-6 sm:pt-10 pb-12 sm:pb-16 flex flex-col items-center scroll-mt-20">
    <!-- Floating Card Wrapper (coordinates shadow & hover lift) -->
    <div class="w-full max-w-4xl relative group py-10 flex flex-col justify-center items-center">
        <!-- Realistic dynamic floor shadow -->
        <div class="absolute bottom-4 left-[10%] w-[80%] h-8 bg-zinc-950/20 dark:bg-black/60 rounded-full pointer-events-none transition-all duration-700 ease-out group-hover:opacity-10 group-hover:scale-x-75 group-hover:blur-[64px] animate-floor-shadow z-0"></div>

        <!-- Bobbing float animation container -->
        <div class="w-full relative z-10 animate-float">
            <!-- Interactive Lift & Glow Container -->
            <div id="auth-card" class="w-full super-glass border border-zinc-200/60 dark:border-zinc-800/60 rounded-[2rem] overflow-hidden transition-all duration-700 ease-out group-hover:-translate-y-4 group-hover:shadow-[0_45px_85px_-20px_rgba(0,0,0,0.22),_0_20px_40px_-25px_rgba(0,0,0,0.15),_0_0_60px_0px_rgba(234,88,12,0.12)] dark:group-hover:shadow-[0_55px_100px_-25px_rgba(0,0,0,0.8),_0_35px_60px_-30px_rgba(0,0,0,0.7),_0_0_65px_0px_rgba(234,88,12,0.08)] shadow-2xl">
        
                <div class="grid grid-cols-1 md:grid-cols-12 min-h-[500px]">
                
                <!-- Left Column: Colored Welcome Panel (Desktop: Left, Mobile: Top) -->
                <div id="green-panel" class="md:col-span-5 bg-emerald-600 dark:bg-emerald-700 text-white flex flex-col justify-center items-center text-center p-8 sm:p-10 relative overflow-hidden rounded-b-[2.5rem] md:rounded-b-none md:rounded-r-[6rem] lg:rounded-r-[8rem] shrink-0 min-h-[220px] md:min-h-none">
                    <!-- Background shapes inside colored panel -->
                    <div class="absolute top-[-20%] left-[-20%] w-60 h-60 bg-white/10 rounded-full blur-2xl"></div>
                    <div class="absolute bottom-[-10%] right-[-10%] w-52 h-52 bg-white/10 rounded-full blur-xl"></div>
                    
                    <div class="relative z-10 space-y-4 max-w-[280px]">
                        <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight leading-tight">Halo, Selamat Datang!</h2>
                        <p class="text-sm text-emerald-100 font-medium font-sans">Belum punya akun? Akun klien dibuat otomatis oleh tim VexaHost setelah proyek Anda disepakati.</p>
                        <div class="pt-2">
                            <a href="https://wa.me/6285808749131" target="_blank" rel="noopener noreferrer" id="btn-contact" class="inline-block px-8 py-2.5 border-2 border-white hover:bg-white hover:text-emerald-700 text-white text-sm font-bold rounded-xl transition-all duration-350 shadow-sm focus:outline-none">
                                Hubungi Tim VexaHost
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Login Form Content (Desktop: Right, Mobile: Bottom) -->
                <div id="form-panel" class="md:col-span-7 flex flex-col justify-center p-8 sm:p-10 lg:p-12">
                    
                    <div class="w-full max-w-md mx-auto space-y-6 flex flex-col">

                        <!-- Heading -->
                        <div class="text-left space-y-1">
                            <h3 class="text-3xl font-black text-zinc-900 dark:text-white tracking-tight">Masuk Portal</h3>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">Satu pintu masuk untuk klien & tim VexaHost. Anda otomatis diarahkan ke Client Panel atau Admin Panel sesuai akun.</p>
                        </div>

                        <x-auth-session-status class="mb-2" :status="session('status')" />

                        @if (session('error'))
                            <div class="flex items-start gap-2 rounded-xl border border-rose-200 dark:border-rose-900/50 bg-rose-50 dark:bg-rose-950/30 px-3.5 py-2.5 text-xs font-medium text-rose-700 dark:text-rose-300">
                                <span class="material-symbols-outlined text-[18px] shrink-0">error</span>
                                <span>{{ session('error') }}</span>
                            </div>
                        @endif

                        <!-- Login dengan Google -->
                        <a href="{{ route('auth.google') }}"
                           class="w-full flex items-center justify-center gap-2.5 px-4 py-3 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 hover:bg-zinc-50 dark:hover:bg-zinc-900 text-sm font-bold text-zinc-800 dark:text-zinc-100 shadow-sm transition-all">
                            <svg class="w-[18px] h-[18px] shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z"/>
                                <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.24v3.15C3.26 21.4 7.34 24 12 24z"/>
                                <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.24C.45 8.15 0 9.92 0 12s.45 3.85 1.24 5.42l4.04-3.15z"/>
                                <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.34 0 3.26 2.6 1.24 6.58l4.04 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                            </svg>
                            <span>Lanjutkan dengan Google</span>
                        </a>

                        <!-- Divider -->
                        <div class="relative select-none">
                            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                <div class="w-full border-t border-zinc-200 dark:border-zinc-800/80"></div>
                            </div>
                            <div class="relative flex justify-center text-[11px] font-semibold uppercase tracking-wider">
                                <span class="bg-white dark:bg-zinc-900 px-3 text-zinc-400 dark:text-zinc-500">atau masuk dengan email</span>
                            </div>
                        </div>
                        <!-- Form -->
                        <form method="POST" action="{{ route('login') }}" class="space-y-4">
                            @csrf

                            <!-- Username / Email -->
                            <div class="space-y-1">
                                <label for="email" class="block text-xs font-bold font-mono tracking-wider text-zinc-500 uppercase">Alamat Email</label>
                                <div class="relative">
                                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="nama@email.com"
                                           class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-zinc-50 placeholder-zinc-400 dark:placeholder-zinc-600 focus:border-emerald-600 dark:focus:border-emerald-500 focus:ring focus:ring-emerald-600/20 dark:focus:ring-emerald-500/20 transition-all pl-4 pr-10 py-3 text-sm">
                                    <span class="material-symbols-outlined text-[20px] text-zinc-400 dark:text-zinc-600 absolute right-3.5 top-1/2 -translate-y-1/2 select-none pointer-events-none">person</span>
                                </div>
                                @error('email')
                                    <p class="text-rose-600 dark:text-rose-400 text-xs mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="space-y-1">
                                <label for="password" class="block text-xs font-bold font-mono tracking-wider text-zinc-500 uppercase">Kata Sandi</label>
                                <div class="relative">
                                    <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••"
                                           class="w-full bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-zinc-900 dark:text-zinc-50 placeholder-zinc-400 dark:placeholder-zinc-600 focus:border-emerald-600 dark:focus:border-emerald-500 focus:ring focus:ring-emerald-600/20 dark:focus:ring-emerald-500/20 transition-all pl-4 pr-10 py-3 text-sm">
                                    <span class="material-symbols-outlined text-[20px] text-zinc-400 dark:text-zinc-600 absolute right-3.5 top-1/2 -translate-y-1/2 select-none pointer-events-none">lock</span>
                                </div>
                                @error('password')
                                    <p class="text-rose-600 dark:text-rose-400 text-xs mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Remember & Forgot Password -->
                            <div class="flex items-center justify-between pt-1">
                                <label for="remember_me" class="inline-flex items-center cursor-pointer select-none">
                                    <input id="remember_me" type="checkbox" name="remember" class="rounded bg-zinc-50 dark:bg-zinc-950 border-zinc-200 dark:border-zinc-800 text-emerald-600 dark:text-emerald-500 focus:ring-emerald-500/40 focus:ring-offset-zinc-50 dark:focus:ring-offset-zinc-900">
                                    <span class="ms-2 text-xs text-zinc-500 dark:text-zinc-400 font-semibold">Ingat Saya</span>
                                </label>

                                @if (Route::has('password.request'))
                                    <a class="text-xs text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors font-bold" href="{{ route('password.request') }}">
                                        Lupa Kata Sandi?
                                    </a>
                                @endif
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-2">
                                <button type="submit" class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl shadow-md shadow-emerald-600/10 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600 dark:focus:ring-emerald-500 focus:ring-offset-white dark:focus:ring-offset-zinc-900 transition-all">
                                    Masuk Dashboard
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    </section>

    <!-- ================================================================= -->
    <!-- 2. LANDING PAGE                                                     -->
    <!-- ================================================================= -->
    @include('landing.about')

    @include('landing.sections')

    @include('landing.payments')
</x-landing-layout>

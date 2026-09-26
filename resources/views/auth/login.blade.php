<x-landing-layout :flush="true">
    {{-- =================================================================
         1. HERO LOGIN — tepat satu layar (100svh), section berikutnya baru
            terlihat setelah digulir. Bentuk dekoratif bersudut tegas
            (radius kecil), bukan lingkaran, mengikuti gaya UI VexaHost.
         ================================================================= --}}
    <section id="masuk" class="relative isolate w-full min-h-[100svh] flex items-center overflow-hidden scroll-mt-0 bg-zinc-50 dark:bg-zinc-950 transition-colors duration-300">

        {{-- ---------- Latar & bentuk dekoratif ---------- --}}
        <div class="absolute inset-0 -z-10 pointer-events-none" aria-hidden="true">
            {{-- Bentuk di belakang panel (mengintip dari tepi) --}}
            <div class="absolute top-16 -left-16 sm:-left-10 w-40 h-40 sm:w-56 sm:h-56 rounded-[1.5rem] rotate-12 bg-gradient-to-br from-[#d4fb63] to-[#BAFF39] dark:from-[#95dc19] dark:to-[#6ba30e] opacity-90 hidden sm:block"></div>
            <div class="absolute -top-20 right-[8%] w-48 h-48 sm:w-72 sm:h-72 rounded-[1.75rem] -rotate-[18deg] bg-gradient-to-br from-[#c0f943] to-[#9ada1b] dark:from-[#7bbd11] dark:to-[#558609] opacity-80 hidden sm:block"></div>
            <div class="absolute -bottom-24 -right-12 w-56 h-56 sm:w-80 sm:h-80 rounded-[2rem] rotate-[24deg] bg-gradient-to-tl from-[#BAFF39] to-[#d4fb63] dark:from-[#6ba30e] dark:to-[#95dc19] opacity-90 hidden sm:block"></div>

            {{-- Panel utama (area hero) — lebar & tinggi dibatasi agar proporsional di monitor besar --}}
            <div class="absolute overflow-hidden rounded-[1.25rem] sm:rounded-[1.75rem]
                        left-3 right-3 top-[4.5rem] bottom-3
                        sm:left-6 sm:right-6 sm:top-20 sm:bottom-6
                        lg:left-[max(2.5rem,calc((100vw_-_84rem)/2))] lg:right-[max(2.5rem,calc((100vw_-_84rem)/2))]
                        lg:top-[max(5.5rem,calc((100svh_-_46rem)/2))] lg:bottom-[max(2rem,calc((100svh_-_46rem)/2_-_2rem))]
                        2xl:left-[max(3rem,calc((100vw_-_100rem)/2))] 2xl:right-[max(3rem,calc((100vw_-_100rem)/2))]
                        2xl:top-[max(6rem,calc((100svh_-_54rem)/2))] 2xl:bottom-[max(2.5rem,calc((100svh_-_54rem)/2_-_2rem))]
                        bg-gradient-to-br from-[#c8ff54] via-[#BAFF39] to-[#99db1e]
                        dark:from-[#bbf83f] dark:via-[#BAFF39] dark:to-[#8cd515]
                        shadow-2xl shadow-[#BAFF39]/20">
                {{-- Bentuk transparan di dalam panel --}}
                <div class="absolute -bottom-40 -left-28 w-[30rem] h-[30rem] rounded-[3rem] rotate-12 bg-black/5 dark:bg-black/10"></div>
                <div class="absolute -top-24 left-[38%] w-72 h-72 rounded-[2rem] rotate-45 bg-black/[0.04] dark:bg-black/[0.08]"></div>
                <div class="absolute top-[18%] -right-20 w-80 h-80 rounded-[2.5rem] -rotate-12 bg-white/20"></div>
                <div class="absolute bottom-10 right-[42%] w-24 h-24 rounded-2xl rotate-[30deg] border-2 border-black/10 hidden lg:block"></div>
                <div class="absolute inset-0 bg-grid-pattern opacity-15"></div>
            </div>
        </div>

        {{-- ---------- Konten ---------- --}}
        <div class="relative w-full max-w-6xl 2xl:max-w-[80rem] mx-auto
                    px-7 sm:px-14 lg:px-20
                    pt-24 pb-10 sm:pt-28 sm:pb-14 lg:py-24
                    grid grid-cols-1 lg:grid-cols-2 items-center
                    gap-6 sm:gap-8 lg:gap-16 text-zinc-950">

            {{-- Kiri: sambutan --}}
            <div class="space-y-3 sm:space-y-4 text-center lg:text-left">
                <h1 class="text-3xl sm:text-4xl xl:text-5xl 2xl:text-6xl font-black tracking-tight leading-[1.1] uppercase text-zinc-950">
                    Halo, Selamat Datang!
                </h1>
                <p class="text-sm sm:text-base xl:text-lg font-bold text-zinc-900">
                    Satu pintu masuk untuk klien & tim VexaHost.
                </p>
                <p class="hidden sm:block text-sm text-zinc-800/90 leading-relaxed max-w-md mx-auto lg:mx-0 [@media(max-height:640px)]:hidden">
                    Belum punya akun? Akun klien dibuat otomatis oleh tim VexaHost setelah proyek Anda disepakati.
                    Anda otomatis diarahkan ke Client Panel atau Admin Panel sesuai akun.
                </p>
                <div class="hidden lg:block pt-2">
                    <a href="https://wa.me/6285808749131" target="_blank" rel="noopener noreferrer" id="btn-contact"
                       class="inline-flex items-center gap-2 px-6 py-2.5 border-2 border-zinc-950 hover:bg-zinc-950 hover:text-[#BAFF39] text-zinc-950 text-sm font-bold rounded-lg transition-colors">
                        <x-icon-whatsapp class="w-4 h-4" fill="currentColor" />
                        Hubungi Tim VexaHost
                    </a>
                </div>
            </div>

            {{-- Kanan: form masuk --}}
            <div class="w-full max-w-md 2xl:max-w-lg mx-auto lg:mx-0 lg:justify-self-end">
                <div class="space-y-4 sm:space-y-5 [@media(max-height:720px)]:space-y-3">
                    <div class="space-y-1">
                        <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-zinc-950">Masuk Portal</h2>
                        <p class="text-xs text-zinc-800 font-medium">Gunakan akun Google atau email yang sudah terdaftar.</p>
                    </div>

                    <x-auth-session-status class="rounded-lg bg-zinc-950 px-3.5 py-2.5 !text-[#BAFF39] font-bold" :status="session('status')" />

                    @if (session('error'))
                        <div class="flex items-start gap-2 rounded-lg bg-white px-3.5 py-2.5 text-xs font-semibold text-rose-700 shadow-sm">
                            <span class="material-symbols-outlined text-[18px] shrink-0">error</span>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    {{-- Login dengan Google --}}
                    <a href="{{ route('auth.google') }}"
                       class="w-full flex items-center justify-center gap-2.5 px-4 py-3 [@media(max-height:720px)]:py-2.5 rounded-lg bg-white hover:bg-zinc-100 text-sm font-bold text-zinc-800 shadow-sm transition-colors">
                        <svg class="w-[18px] h-[18px] shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z"/>
                            <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.24v3.15C3.26 21.4 7.34 24 12 24z"/>
                            <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.24C.45 8.15 0 9.92 0 12s.45 3.85 1.24 5.42l4.04-3.15z"/>
                            <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.34 0 3.26 2.6 1.24 6.58l4.04 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                        </svg>
                        <span>Lanjutkan dengan Google</span>
                    </a>

                    <div class="flex items-center gap-3 select-none" aria-hidden="true">
                        <span class="h-px flex-1 bg-zinc-950/20"></span>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-zinc-800">atau masuk dengan email</span>
                        <span class="h-px flex-1 bg-zinc-950/20"></span>
                    </div>

                    <form method="POST" action="{{ route('login') }}" class="space-y-3.5 [@media(max-height:720px)]:space-y-2.5" x-data="{ lihat: false }">
                        @csrf

                        <div class="space-y-1">
                            <label for="email" class="sr-only">Alamat Email</label>
                            <div class="relative">
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="Alamat email"
                                       class="w-full rounded-lg border border-zinc-950/30 bg-black/5 text-zinc-950 placeholder-zinc-700/80 focus:border-zinc-950 focus:ring-2 focus:ring-zinc-950/20 focus:bg-white/40 transition-all pl-4 pr-11 py-3 [@media(max-height:720px)]:py-2.5 text-sm font-medium">
                                <span class="material-symbols-outlined text-[20px] text-zinc-700 absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none">person</span>
                            </div>
                            @error('email')
                                <p class="text-xs font-semibold text-white bg-rose-600/80 rounded-md px-2 py-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1">
                            <label for="password" class="sr-only">Kata Sandi</label>
                            <div class="relative">
                                <input id="password" :type="lihat ? 'text' : 'password'" type="password" name="password" required autocomplete="current-password" placeholder="Kata sandi"
                                       class="w-full rounded-lg border border-zinc-950/30 bg-black/5 text-zinc-950 placeholder-zinc-700/80 focus:border-zinc-950 focus:ring-2 focus:ring-zinc-950/20 focus:bg-white/40 transition-all pl-4 pr-20 py-3 [@media(max-height:720px)]:py-2.5 text-sm font-medium">
                                <button type="button" @click="lihat = ! lihat"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-[11px] font-bold uppercase tracking-wider text-zinc-900 hover:text-black"
                                        x-text="lihat ? 'Sembunyi' : 'Lihat'">Lihat</button>
                            </div>
                            @error('password')
                                <p class="text-xs font-semibold text-white bg-rose-600/80 rounded-md px-2 py-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between gap-3 pt-0.5">
                            <label for="remember_me" class="inline-flex items-center cursor-pointer select-none">
                                <input id="remember_me" type="checkbox" name="remember"
                                       class="rounded border-zinc-950/40 bg-black/5 text-zinc-950 focus:ring-zinc-950/30 focus:ring-offset-0">
                                <span class="ms-2 text-xs font-semibold text-zinc-900">Ingat Saya</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs font-bold text-zinc-950 underline-offset-4 hover:underline">
                                    Lupa Kata Sandi?
                                </a>
                            @endif
                        </div>

                        <button type="submit"
                                class="w-full flex justify-center items-center py-3 [@media(max-height:720px)]:py-2.5 px-4 rounded-lg bg-zinc-950 hover:bg-black text-sm font-black text-[#BAFF39] shadow-md shadow-black/20 focus:outline-none focus:ring-2 focus:ring-zinc-950 focus:ring-offset-2 focus:ring-offset-[#BAFF39] transition-colors">
                            Masuk Dashboard
                        </button>
                    </form>

                    <p class="text-center lg:text-left text-xs text-zinc-800">
                        Belum punya akun?
                        <a href="https://wa.me/6285808749131" target="_blank" rel="noopener noreferrer" class="font-bold text-zinc-950 underline-offset-4 hover:underline">Hubungi Tim VexaHost</a>
                    </p>
                </div>
            </div>
        </div>

        {{-- Petunjuk gulir --}}
        <a href="#tentang" class="hidden lg:flex [@media(max-height:760px)]:!hidden absolute bottom-3 left-1/2 -translate-x-1/2 items-center gap-1 text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 hover:text-[#5c8a0f] dark:hover:text-[#BAFF39] transition-colors" aria-label="Gulir ke bawah">
            <span class="material-symbols-outlined text-[18px] animate-bounce">keyboard_arrow_down</span>
        </a>
    </section>

    {{-- =================================================================
         2. LANDING PAGE
         ================================================================= --}}
    @include('landing.about')

    @include('landing.sections')

    @include('landing.payments')
</x-landing-layout>

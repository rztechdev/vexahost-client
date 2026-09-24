@php
    $waLink = 'https://wa.me/6285808749131?text=' . rawurlencode('Halo VexaHost, saya butuh bantuan terkait akun / proyek saya.');
    $beranda = request()->routeIs('home', 'login') ? '' : url('/');
@endphp

<footer class="relative border-t border-zinc-200 dark:border-zinc-800/80 bg-zinc-50/80 dark:bg-zinc-950/80 py-10 sm:py-16 px-4 sm:px-6 lg:px-8 transition-colors duration-300 overflow-hidden">
    <div class="absolute inset-0 bg-grid-pattern pointer-events-none opacity-70 dark:opacity-40"></div>

    <div class="max-w-7xl mx-auto w-full relative z-10 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10 lg:gap-8 pb-8 sm:pb-12 border-b border-zinc-200 dark:border-zinc-800">

        <!-- Brand -->
        <div class="lg:col-span-4 space-y-4">
            <a href="{{ url('/') }}" class="inline-block rounded-xl focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600">
                <x-vh-logo size="lg" />
            </a>
            <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed max-w-sm">
                Portal resmi klien & tim VexaHost. Pantau progres proyek website, tiket bantuan, tagihan, dan masa berlaku layanan Anda dalam satu tempat.
            </p>

            <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-zinc-200 dark:border-zinc-700/60 bg-white dark:bg-zinc-900 hover:border-[#25D366] text-xs sm:text-sm font-semibold text-zinc-700 dark:text-zinc-200 transition-colors">
                <x-icon-whatsapp class="w-4 h-4" />
                <span>Chat WhatsApp</span>
            </a>

            <div class="flex">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 font-mono text-[10px] text-zinc-600 dark:text-zinc-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#25D366] animate-pulse"></span>
                    <span>ONLINE • Fast Response</span>
                </div>
            </div>
        </div>

        <!-- Layanan -->
        <div class="lg:col-span-3 space-y-3">
            <h5 class="text-xs font-bold font-mono tracking-wider text-zinc-400 dark:text-zinc-500 uppercase">Layanan VexaHost</h5>
            <ul class="space-y-2.5 text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">
                <li><a href="https://build.vexahostcloud.my.id" target="_blank" rel="noopener noreferrer" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Jasa Pembuatan Website</a></li>
                <li><a href="https://wa.vexahostcloud.my.id" target="_blank" rel="noopener noreferrer" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">WhatsApp Gateway API</a></li>
                <li><a href="https://vexahostcloud.my.id" target="_blank" rel="noopener noreferrer" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Cloud VPS & Hosting</a></li>
            </ul>
        </div>

        <!-- Portal -->
        <div class="lg:col-span-2 space-y-3">
            <h5 class="text-xs font-bold font-mono tracking-wider text-zinc-400 dark:text-zinc-500 uppercase">Portal</h5>
            <ul class="space-y-2.5 text-xs sm:text-sm text-zinc-600 dark:text-zinc-400">
                <li><a href="{{ $beranda }}#masuk" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Masuk Portal</a></li>
                <li><a href="{{ $beranda }}#fitur" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Fitur Utama</a></li>
                <li><a href="{{ $beranda }}#rbac" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Akses Peran</a></li>
                @if (Route::has('password.request'))
                    <li><a href="{{ route('password.request') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Lupa Kata Sandi</a></li>
                @endif
            </ul>
        </div>

        <!-- Hubungi Kami (semua lewat WhatsApp) -->
        <div class="lg:col-span-3 space-y-3">
            <h5 class="text-xs font-bold font-mono tracking-wider text-zinc-400 dark:text-zinc-500 uppercase">Hubungi Kami</h5>
            <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">
                <span class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700/60 flex items-center justify-center shrink-0">
                    <x-icon-whatsapp class="w-4 h-4" />
                </span>
                <span class="font-mono whitespace-nowrap">+62 858-0874-9131</span>
            </a>
            <p class="text-[11px] text-zinc-400 dark:text-zinc-500 leading-relaxed">Pertanyaan akun, tagihan, dan bantuan teknis dilayani lewat WhatsApp.</p>
        </div>
    </div>

    <!-- Watermark & domain -->
    <div class="max-w-7xl mx-auto w-full pt-6 sm:pt-8 flex flex-col lg:flex-row justify-between items-center gap-3 text-xs text-zinc-400 dark:text-zinc-500 font-medium text-center lg:text-left relative z-10">
        <div>&copy; {{ date('Y') }} VexaHost. All rights reserved. Created by vexahostcloud.</div>
        <div class="flex flex-wrap items-center justify-center gap-2 sm:gap-3 font-mono text-[11px]">
            <a href="https://client.vexahostcloud.my.id" class="text-zinc-600 dark:text-zinc-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">client.vexahostcloud.my.id</a>
            <span>•</span>
            <a href="https://build.vexahostcloud.my.id" target="_blank" rel="noopener noreferrer" class="text-zinc-600 dark:text-zinc-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">build.vexahostcloud.my.id ↗</a>
            <span>•</span>
            <a href="https://wa.vexahostcloud.my.id" target="_blank" rel="noopener noreferrer" class="text-zinc-600 dark:text-zinc-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">wa.vexahostcloud.my.id ↗</a>
            <span>•</span>
            <a href="https://vexahostcloud.my.id" target="_blank" rel="noopener noreferrer" class="text-zinc-600 dark:text-zinc-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">vexahostcloud.my.id ↗</a>
        </div>
    </div>
</footer>

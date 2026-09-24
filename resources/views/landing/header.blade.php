@php
    // Halaman utama = form login + landing; dari halaman lain (lupa sandi, dll) jangkar diarahkan ke beranda.
    $beranda = request()->routeIs('home', 'login') ? '' : url('/');

    $menuHalaman = [
        ['Masuk', $beranda . '#masuk'],
        ['Tentang', $beranda . '#tentang'],
        ['Fitur', $beranda . '#fitur'],
        ['Akses Peran', $beranda . '#rbac'],
    ];

    // Produk lain di ekosistem VexaHost (tab baru)
    $menuEkosistem = [
        ['VPS', 'https://vexahostcloud.my.id', 'VexaHost Cloud VPS & Hosting'],
        ['WA Gateway', 'https://wa.vexahostcloud.my.id', 'VexaHost WhatsApp Gateway API'],
        ['Jasa Web', 'https://build.vexahostcloud.my.id', 'VexaHost Build — Jasa Pembuatan Website'],
    ];
@endphp

<div x-data="{ mobileOpen: false, scrolled: false }"
     x-init="scrolled = (window.scrollY > 20); $watch('mobileOpen', val => document.body.classList.toggle('overflow-hidden', val))"
     @scroll.window.passive="scrolled = (window.scrollY > 20)">

    <!-- Header: transparan saat di paling atas, berlatar saat halaman digulir -->
    <header class="fixed top-0 left-0 right-0 z-50 w-full transition-all duration-300"
            :class="scrolled
                ? 'py-2.5 sm:py-3 bg-white/90 dark:bg-zinc-950/90 backdrop-blur-md border-b border-zinc-200/80 dark:border-zinc-800/80 shadow-xs'
                : 'py-3.5 sm:py-4 bg-transparent border-b border-transparent shadow-none'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between gap-4">
                <!-- Logo (kiri) -->
                <a href="{{ url('/') }}" class="shrink-0 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600">
                    <x-vh-logo size="sm" />
                </a>

                <!-- Grup menu rata kanan -->
                <div class="hidden lg:flex items-center gap-5 xl:gap-7">
                    <nav class="flex items-center gap-5 xl:gap-6 text-sm font-medium text-zinc-700 dark:text-zinc-300" aria-label="Navigasi Utama">
                        @foreach ($menuHalaman as [$label, $href])
                            <a href="{{ $href }}" class="group flex items-center py-1 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                                <span class="relative pb-0.5">
                                    {{ $label }}
                                    <span class="absolute bottom-0 left-0 h-[2px] w-0 bg-emerald-600 transition-all duration-200 group-hover:w-full"></span>
                                </span>
                            </a>
                        @endforeach

                        @foreach ($menuEkosistem as [$label, $href, $title])
                            <a href="{{ $href }}" target="_blank" rel="noopener noreferrer" title="{{ $title }}"
                               class="group flex items-center gap-1 py-1 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                                <span class="relative pb-0.5">
                                    {{ $label }}
                                    <span class="absolute bottom-0 left-0 h-[2px] w-0 bg-emerald-600 transition-all duration-200 group-hover:w-full"></span>
                                </span>
                                <svg class="w-3 h-3 text-zinc-400 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17L17 7H7M17 7V17"/>
                                </svg>
                            </a>
                        @endforeach
                    </nav>

                    <!-- Tema terang/gelap -->
                    <button type="button" onclick="toggleTheme()" title="Ganti Tema"
                            class="p-2 rounded-xl text-zinc-500 dark:text-zinc-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-zinc-100 dark:hover:bg-zinc-800/80 transition-colors focus:outline-none">
                        <span class="material-symbols-outlined text-[20px] block dark:hidden">dark_mode</span>
                        <span class="material-symbols-outlined text-[20px] hidden dark:block text-amber-400">light_mode</span>
                    </button>

                    @auth
                        <a href="{{ auth()->user()->homeUrl() }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold shadow-xs transition-all active:scale-95">
                            Dasbor
                        </a>
                    @else
                        <a href="{{ $beranda }}#masuk" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold shadow-xs transition-all active:scale-95">
                            Masuk
                        </a>
                    @endauth
                </div>

                <!-- Aksi mobile -->
                <div class="flex lg:hidden items-center gap-1.5">
                    <button type="button" onclick="toggleTheme()" title="Ganti Tema"
                            class="p-2 rounded-xl text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                        <span class="material-symbols-outlined text-[20px] block dark:hidden">dark_mode</span>
                        <span class="material-symbols-outlined text-[20px] hidden dark:block text-amber-400">light_mode</span>
                    </button>
                    <button type="button" @click="mobileOpen = true" aria-label="Buka Menu"
                            class="p-2 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors">
                        <span class="material-symbols-outlined text-[20px] block">menu</span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Drawer mobile -->
    <div x-show="mobileOpen" x-cloak style="display: none;"
         x-transition:enter="transition-opacity duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[60] flex flex-col bg-white dark:bg-zinc-950 lg:hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-zinc-200 dark:border-zinc-800">
            <a href="{{ url('/') }}" @click="mobileOpen = false"><x-vh-logo size="xs" /></a>
            <button type="button" @click="mobileOpen = false" aria-label="Tutup Menu"
                    class="p-2 rounded-xl text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                <span class="material-symbols-outlined text-[22px] block">close</span>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto px-6 py-5 space-y-6">
            <div class="space-y-1">
                <p class="text-[11px] font-bold font-mono uppercase tracking-wider text-zinc-400">Portal</p>
                @foreach ($menuHalaman as [$label, $href])
                    <a href="{{ $href }}" @click="mobileOpen = false" class="block py-1.5 text-sm font-medium text-zinc-700 dark:text-zinc-200 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">{{ $label }}</a>
                @endforeach
            </div>
            <div class="space-y-1">
                <p class="text-[11px] font-bold font-mono uppercase tracking-wider text-zinc-400">Ekosistem VexaHost</p>
                @foreach ($menuEkosistem as [$label, $href, $title])
                    <a href="{{ $href }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-between py-1.5 text-sm font-medium text-zinc-700 dark:text-zinc-200 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                        <span>{{ $title }}</span>
                        <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17L17 7H7M17 7V17"/></svg>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="shrink-0 border-t border-zinc-200 dark:border-zinc-800 p-4 pb-[max(1rem,env(safe-area-inset-bottom))]">
            @auth
                <a href="{{ auth()->user()->homeUrl() }}" class="block w-full text-center rounded-xl bg-emerald-600 hover:bg-emerald-700 px-4 py-2.5 text-sm font-semibold text-white">Dasbor</a>
            @else
                <a href="{{ $beranda }}#masuk" @click="mobileOpen = false" class="block w-full text-center rounded-xl bg-emerald-600 hover:bg-emerald-700 px-4 py-2.5 text-sm font-semibold text-white">Masuk</a>
            @endauth
        </div>
    </div>
</div>

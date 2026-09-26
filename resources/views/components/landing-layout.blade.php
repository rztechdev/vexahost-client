@props(['title' => null, 'flush' => false])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-zinc-50 dark:bg-zinc-950">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <meta name="color-scheme" content="light dark">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'VexaHost Client') . ' | Masuk Admin & Client Panel' }}</title>
    <meta name="description" content="VexaHost Client — satu pintu untuk klien & tim VexaHost memantau proyek website, tiket bantuan, tagihan, dan progres pengerjaan.">
    @include('partials.favicon')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    <script>
        // Inline theme check to prevent flickering (FOUC)
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased overflow-x-hidden relative min-h-screen flex flex-col justify-between bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-50 transition-colors duration-300"
      x-data="welcomeLayout">

    <!-- Decorative background grid -->
    <div class="fixed inset-0 bg-grid-pattern pointer-events-none opacity-40 dark:opacity-20 z-0"></div>

    @include('landing.header')

    <!-- Main Content Area -->
    {{-- flush: konten pertama (hero) mengisi layar di bawah header transparan --}}
    <main class="flex-1 flex flex-col w-full relative z-10 overflow-x-hidden {{ $flush ? '' : 'pt-20 sm:pt-24' }}">
        {{ $slot }}
    </main>

    @include('landing.footer')

    <!-- Floating WhatsApp Action Button (sama dengan VexaHost Build) -->
    <div class="fixed bottom-6 right-6 z-40">
        <a 
            href="https://wa.me/6285808749131?text=Halo%20VexaHost,%20saya%20tertarik%20untuk%20konsultasi%20pembuatan%20website%20untuk%20usaha%20saya."
            target="_blank"
            rel="noopener noreferrer"
            class="group flex items-center gap-2 px-4 py-2.5 bg-[#25D366] hover:bg-[#20bd5a] text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 active:scale-95 border border-white/80 dark:border-zinc-800"
            aria-label="Konsultasi WhatsApp"
        >
            <x-icon-whatsapp class="w-5 h-5 fill-current animate-pulse" />
            <span class="text-xs font-bold tracking-wide">Konsultasi</span>
        </a>
    </div>

    @stack('scripts')
</body>
</html>

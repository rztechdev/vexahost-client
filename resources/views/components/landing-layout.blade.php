@props(['title' => null, 'flush' => false])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
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

    <!-- Decorative background grid (Clean neutral on white) -->
    <div class="fixed inset-0 bg-grid-pattern z-[-1] pointer-events-none"></div>

    @include('landing.header')

    <!-- Main Content Area -->
    {{-- flush: konten pertama (hero) mengisi layar di bawah header transparan --}}
    <main class="flex-1 flex flex-col w-full relative overflow-x-hidden {{ $flush ? '' : 'pt-20 sm:pt-24' }}">
        {{ $slot }}
    </main>

    @include('landing.footer')

    @stack('scripts')
</body>
</html>

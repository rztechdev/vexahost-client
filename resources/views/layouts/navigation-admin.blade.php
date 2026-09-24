@php
    $authUser = Auth::user();

    $overdueCount = $authUser->can('leads.manage')
        ? \App\Models\Lead::whereNotNull('follow_up_date')->where('follow_up_date', '<', now()->toDateString())->whereNotIn('status', ['deal', 'tidak_lanjut'])->count()
        : 0;

    try {
        $expiringSoonCount = $authUser->can('maintenance.manage')
            ? \App\Models\ProjectSubscription::whereIn('status', ['akan_expired', 'expired'])->count()
            : 0;
    } catch (\Exception $e) {
        $expiringSoonCount = 0;
    }

    $verifyingInvoices = $authUser->can('invoices.manage')
        ? \App\Models\Invoice::where('status', 'verifying')->count()
        : 0;

    // Definisi menu Admin Panel: [label, route, pola aktif, ikon, izin, badge, warna badge]
    $menuSections = [
        'Ringkasan' => [
            ['Dashboard CRM', 'admin.dashboard', 'admin.dashboard', 'dashboard', 'crm.dashboard', 0, null],
            ['Dashboard Operasional', 'admin.operations', 'admin.operations', 'monitoring', 'dashboard.view', 0, null],
        ],
        'Penjualan & Keuangan' => [
            ['Leads & Pipeline', 'admin.leads.index', 'admin.leads.*', 'group', 'leads.manage', $overdueCount, 'bg-rose-500'],
            ['Proyek Website', 'admin.projects.index', 'admin.projects.*', 'web', 'crm.projects.manage', 0, null],
            ['Pembayaran & DP', 'admin.payments.index', 'admin.payments.*', 'payments', 'payments.manage', 0, null],
            ['Invoice Klien', 'invoices.index', 'invoices.*', 'receipt_long', 'invoices.manage', $verifyingInvoices, 'bg-amber-500'],
            ['Maintenance Bulanan', 'admin.maintenance.index', 'admin.maintenance.*', 'published_with_changes', 'maintenance.manage', 0, null],
            ['Masa Berlaku / Lisensi', 'admin.subscriptions.index', 'admin.subscriptions.*', 'license', 'maintenance.manage', $expiringSoonCount, 'bg-amber-500'],
            ['Riwayat Pesan WA', 'admin.messages.index', 'admin.messages.*', 'chat', 'messages.manage', 0, null],
        ],
        'Operasional Klien' => [
            ['Papan Kanban Proyek', 'projects.index', ['projects.*', 'tasks.*'], 'view_kanban', 'projects.view', 0, null],
            ['Kelola Tiket', 'admin.tickets', 'admin.tickets', 'inbox', 'tickets.manage', 0, null],
            ['Tiket Saya', 'technician.tickets', 'technician.tickets', 'support_agent', 'tickets.handle', 0, null],
        ],
        'Tata Kelola & Tim' => [
            ['Activity & Audit Log', 'admin.activity-logs.index', 'admin.activity-logs.*', 'history', 'activity.view', 0, null],
            ['Kelola Pengguna', 'admin.users.index', 'admin.users.*', 'manage_accounts', 'users.manage', 0, null],
            ['Role & Hak Akses', 'admin.roles.index', 'admin.roles.*', 'admin_panel_settings', 'roles.manage', 0, null],
            ['Pengaturan Perusahaan', 'admin.settings.company.edit', 'admin.settings.company.*', 'corporate_fare', 'settings.manage', 0, null],
        ],
    ];

    $visibleSections = [];
    foreach ($menuSections as $section => $items) {
        $items = array_values(array_filter($items, fn ($item) => $authUser->can($item[4])));
        if ($items) {
            $visibleSections[$section] = $items;
        }
    }

    $homeRoute = $authUser->can('crm.dashboard') ? 'admin.dashboard' : 'admin.operations';
@endphp

<!-- ========================================================================= -->
<!-- 1. DESKTOP SIDEBAR (Visible ONLY on Desktop lg:flex) -->
<!-- ========================================================================= -->
<aside class="hidden lg:flex fixed top-0 left-0 z-50 h-screen w-64 bg-white/95 dark:bg-zinc-950/95 backdrop-blur-xl border-r border-zinc-200/80 dark:border-zinc-900/80 flex-col shadow-xs">

    <!-- Branding Header -->
    <div class="h-16 shrink-0 flex items-center justify-between px-6 border-b border-zinc-200/60 dark:border-zinc-800/60 bg-white/50 dark:bg-zinc-950/50 backdrop-blur-md">
        <a href="{{ route($homeRoute) }}" class="flex items-center gap-2.5 group font-sans">
            <img src="{{ asset('images/logo.png') }}" alt="VexaHost Logo" class="h-8 w-auto object-contain group-hover:scale-105 transition-transform duration-150">
            <div class="flex flex-col">
                <span class="text-sm font-black text-zinc-900 dark:text-white tracking-tight leading-none">Vexa<span class="text-emerald-600 dark:text-emerald-400">Host</span></span>
                <span class="text-[9px] font-mono text-zinc-400 dark:text-zinc-500 font-bold uppercase tracking-wider mt-0.5">Admin Panel</span>
            </div>
        </a>
    </div>

    <!-- Navigation Menu items -->
    <nav class="flex-1 overflow-y-auto custom-scrollbar py-5 space-y-1 px-4">
        @foreach($visibleSections as $section => $items)
            <div class="flex items-center gap-2 px-3.5 py-2 {{ $loop->first ? '' : 'mt-3' }} mb-1">
                <span class="text-[9px] font-bold font-mono text-zinc-400 dark:text-zinc-500 uppercase tracking-widest">{{ $section }}</span>
                <div class="h-px bg-zinc-200/60 dark:bg-zinc-800/60 flex-1"></div>
            </div>

            @foreach($items as [$label, $routeName, $pattern, $icon, $permission, $badge, $badgeColor])
                @php $isActive = request()->routeIs(...(array) $pattern); @endphp
                <a href="{{ route($routeName) }}"
                   class="flex items-center px-3.5 py-2.5 justify-start gap-3 rounded-lg text-xs transition-colors duration-150 group {{ $isActive ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-900/70 hover:text-zinc-900 dark:hover:text-zinc-100 font-medium' }}">
                    <span class="material-symbols-outlined text-[20px] {{ $isActive ? 'text-white' : 'text-zinc-400 dark:text-zinc-500 group-hover:text-zinc-700 dark:group-hover:text-zinc-300' }} shrink-0">{{ $icon }}</span>
                    <span class="truncate flex-1">{{ $label }}</span>
                    @if($badge > 0)
                        <span class="px-1.5 py-0.5 text-[10px] font-mono font-bold rounded-md {{ $isActive ? 'bg-white/20 text-white' : $badgeColor . ' text-white' }} leading-none">{{ $badge }}</span>
                    @endif
                </a>
            @endforeach
        @endforeach

        <!-- Quick Snippets Shortcut in Sidebar -->
        @can('messages.manage')
            <div class="pt-4 px-2">
                <button @click="$dispatch('open-quick-snippets')"
                        class="w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-lg bg-zinc-100 dark:bg-zinc-900 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 text-zinc-700 dark:text-zinc-300 hover:text-emerald-700 dark:hover:text-emerald-400 border border-zinc-200 dark:border-zinc-800 hover:border-emerald-500/40 text-xs font-bold transition-all shadow-xs">
                    <span class="material-symbols-outlined text-[18px] text-emerald-600">content_paste</span>
                    <span>Template Chat WA</span>
                </button>
            </div>
        @endcan
    </nav>
</aside>

<!-- ========================================================================= -->
<!-- 2. TOP HEADER BAR -->
<!-- ========================================================================= -->
<header class="fixed top-0 right-0 left-0 lg:left-64 h-16 bg-white/80 dark:bg-zinc-950/80 backdrop-blur-md border-b border-zinc-200 dark:border-zinc-800/80 flex items-center justify-between px-4 sm:px-8 z-30">

    <div class="flex items-center gap-2.5">
        <a href="{{ route($homeRoute) }}" class="lg:hidden flex items-center gap-2">
            <img src="{{ asset('images/logo.png') }}" alt="VexaHost" class="h-6 w-auto">
            <span class="text-xs font-bold text-zinc-900 dark:text-white">VexaHost Admin</span>
        </a>
        <span class="hidden lg:inline text-xs font-semibold text-zinc-500 dark:text-zinc-400">
            VexaHost &middot; Admin Panel
        </span>
    </div>

    <div class="flex items-center gap-2 sm:gap-4">

        <!-- Theme Toggle Button -->
        <button @click="toggleTheme()"
                class="p-2 rounded-lg text-zinc-500 dark:text-zinc-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-zinc-100 dark:hover:bg-zinc-800/50 transition-colors focus:outline-none cursor-pointer"
                title="Ganti Tema">
            <span class="material-symbols-outlined text-[22px] block" x-show="!darkMode">light_mode</span>
            <span class="material-symbols-outlined text-[22px] block text-amber-400" x-show="darkMode" style="display: none;">dark_mode</span>
        </button>

        <!-- Menu akun (avatar saja; detail di dropdown) -->
        <x-profile-menu />
    </div>
</header>

<!-- ========================================================================= -->
<!-- 3. MOBILE BOTTOM NAVIGATION & DRAWER -->
<!-- ========================================================================= -->
@php
    $bottomItems = array_values(array_filter([
        ['Dashboard', $homeRoute, ['admin.dashboard', 'admin.operations'], 'dashboard', 'dashboard.view'],
        ['Leads', 'admin.leads.index', 'admin.leads.*', 'group', 'leads.manage'],
        ['Proyek', 'admin.projects.index', 'admin.projects.*', 'web', 'crm.projects.manage'],
        ['Kanban', 'projects.index', ['projects.*', 'tasks.*'], 'view_kanban', 'projects.view'],
    ], fn ($item) => $authUser->can($item[4])));
    $bottomItems = array_slice($bottomItems, 0, 3);
@endphp
<div x-data="{ mobileMenuOpen: false }">

    <!-- Fixed Bottom Navigation Bar -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 dark:bg-zinc-950/95 backdrop-blur-lg border-t border-zinc-200 dark:border-zinc-800 h-16 flex items-center justify-around px-2 shadow-lg select-none">
        @foreach($bottomItems as [$label, $routeName, $pattern, $icon])
            @php $isActiveMobile = request()->routeIs(...(array) $pattern); @endphp
            <a href="{{ route($routeName) }}"
               class="flex flex-col items-center justify-center flex-1 h-full py-1 transition-colors relative {{ $isActiveMobile ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200 font-medium' }}">
                <span class="material-symbols-outlined text-[22px] {{ $isActiveMobile ? 'scale-110 font-bold' : '' }} transition-transform">{{ $icon }}</span>
                <span class="text-[10px] mt-0.5 tracking-tight">{{ $label }}</span>
                @if($routeName === 'admin.leads.index' && $overdueCount > 0)
                    <span class="absolute top-2 right-4 w-2 h-2 rounded-full bg-rose-500"></span>
                @endif
            </a>
        @endforeach

        <!-- Menu / Lainnya (Paling Kanan) -->
        <button type="button" @click="mobileMenuOpen = true"
                class="flex flex-col items-center justify-center flex-1 h-full py-1 transition-colors text-zinc-500 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200 font-medium">
            <span class="material-symbols-outlined text-[22px]">grid_view</span>
            <span class="text-[10px] mt-0.5 tracking-tight">Menu</span>
        </button>
    </nav>

    <!-- Slide-Up Sheet Modal -->
    <div x-show="mobileMenuOpen"
         x-cloak
         class="fixed inset-0 z-50 flex flex-col justify-end"
         style="display: none;">

        <!-- Backdrop -->
        <div class="fixed inset-0 bg-zinc-950/60 backdrop-blur-sm transition-opacity"
             x-show="mobileMenuOpen"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="mobileMenuOpen = false"></div>

        <!-- Slide-Up Sheet Container -->
        <div class="relative bg-white dark:bg-zinc-900 rounded-t-2xl p-5 border-t border-zinc-200 dark:border-zinc-800 shadow-2xl space-y-4 max-h-[85vh] overflow-y-auto z-10"
             x-show="mobileMenuOpen"
             x-transition:enter="ease-out duration-250 transform"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="ease-in duration-200 transform"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full">

            <!-- Drawer Header Handle -->
            <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-600 text-[22px]">apps</span>
                    <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Menu Admin Panel</h3>
                </div>
                <button type="button" @click="mobileMenuOpen = false" class="p-1 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 rounded-lg">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            @foreach($visibleSections as $section => $items)
                <div class="space-y-2">
                    <p class="text-[9px] font-bold font-mono text-zinc-400 dark:text-zinc-500 uppercase tracking-widest">{{ $section }}</p>
                    <div class="grid grid-cols-2 gap-2.5">
                        @foreach($items as [$label, $routeName, $pattern, $icon])
                            <a href="{{ route($routeName) }}"
                               class="flex items-center gap-2.5 p-3 rounded-xl border border-zinc-200/80 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950/50 hover:border-emerald-500 transition-colors {{ request()->routeIs(...(array) $pattern) ? 'ring-1 ring-emerald-500 font-bold' : '' }}">
                                <span class="material-symbols-outlined text-emerald-600 text-[20px]">{{ $icon }}</span>
                                <span class="text-xs text-zinc-800 dark:text-zinc-200">{{ $label }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach

            @can('messages.manage')
                <button type="button" @click="mobileMenuOpen = false; $dispatch('open-quick-snippets')"
                        class="w-full flex items-center gap-2.5 p-3 rounded-xl border border-zinc-200/80 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950/50 hover:border-emerald-500 text-left transition-colors">
                    <span class="material-symbols-outlined text-emerald-600 text-[20px]">content_paste</span>
                    <span class="text-xs text-zinc-800 dark:text-zinc-200">Template Chat WA</span>
                </button>
            @endcan

            <!-- Profile & Logout Section -->
            <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800 space-y-2">
                <a href="{{ route('profile.edit') }}" class="flex items-center justify-between p-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-950/60 text-xs text-zinc-700 dark:text-zinc-300">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">account_circle</span>
                        <span>Profil Saya ({{ $authUser->name }})</span>
                    </div>
                    <span class="material-symbols-outlined text-[16px] text-zinc-400">arrow_forward</span>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 p-2.5 rounded-xl bg-rose-50 dark:bg-rose-950/30 text-xs font-bold text-rose-600 dark:text-rose-400">
                        <span class="material-symbols-outlined text-[18px]">logout</span>
                        <span>Keluar Akun</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{--
    Menu akun di kanan atas dashboard (Admin & Client Panel).
    Di bilah atas hanya avatar; nama, email, dan peran tampil di dalam dropdown —
    mengikuti pola VexaHost & VexaHost WA Gateway.
--}}
@php
    $akun = auth()->user();
    $peran = $akun->getRoleNames()->map(fn ($r) => \Illuminate\Support\Str::headline($r))->join(', ');
@endphp

<div class="relative" x-data="{ profil: false }" @click.outside="profil = false" @keydown.escape.window="profil = false">
    <button type="button" @click="profil = ! profil"
            class="flex items-center gap-1.5 p-1 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800/60 transition-colors focus:outline-none"
            :aria-expanded="profil" aria-haspopup="true" aria-label="Menu akun" title="{{ $akun->name }}">
        @if ($akun->avatar)
            <img src="{{ $akun->avatar }}" alt="" referrerpolicy="no-referrer"
                 class="w-8 h-8 rounded-full object-cover border border-emerald-200 dark:border-emerald-900/60">
        @else
            <span class="w-8 h-8 rounded-full bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900/60 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0">
                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
        @endif
        <svg class="w-4 h-4 text-zinc-400 transition-transform duration-200" :class="profil ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </button>

    <div x-show="profil" x-cloak style="display: none;" x-transition.opacity.duration.150ms
         class="absolute right-0 z-50 mt-1.5 w-64 overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-lg">
        <div class="px-4 py-3 flex items-center gap-3 border-b border-zinc-100 dark:border-zinc-800">
            @if ($akun->avatar)
                <img src="{{ $akun->avatar }}" alt="" referrerpolicy="no-referrer" class="w-10 h-10 rounded-full object-cover border border-emerald-200 dark:border-emerald-900/60 shrink-0">
            @else
                <span class="w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900/60 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            @endif
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-zinc-900 dark:text-white">{{ $akun->name }}</p>
                <p class="truncate text-xs text-zinc-500 dark:text-zinc-400 font-mono">{{ $akun->email }}</p>
                @if ($peran)
                    <p class="mt-0.5 truncate text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">{{ $peran }}</p>
                @endif
            </div>
        </div>

        <div class="p-1.5 text-sm">
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-zinc-900 dark:hover:text-white transition-colors">
                <span class="material-symbols-outlined text-[18px] text-zinc-400">account_circle</span>
                <span>Profil saya</span>
            </a>

            @if ($akun->isClient())
                <a href="{{ route('invoices.index') }}" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-zinc-900 dark:hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-[18px] text-zinc-400">receipt_long</span>
                    <span>Tagihan saya</span>
                </a>
            @endif

            @can('users.manage')
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-zinc-900 dark:hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-[18px] text-zinc-400">group</span>
                    <span>Kelola pengguna</span>
                </a>
            @endcan

            @can('settings.manage')
                <a href="{{ route('admin.settings.company.edit') }}" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-zinc-900 dark:hover:text-white transition-colors">
                    <span class="material-symbols-outlined text-[18px] text-zinc-400">corporate_fare</span>
                    <span>Pengaturan perusahaan</span>
                </a>
            @endcan

            <a href="https://wa.me/6285808749131" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-zinc-900 dark:hover:text-white transition-colors">
                <x-icon-whatsapp class="w-[18px] h-[18px]" />
                <span>Bantuan via WhatsApp</span>
            </a>

            <form method="POST" action="{{ route('logout') }}" class="mt-1 border-t border-zinc-100 dark:border-zinc-800 pt-1.5">
                @csrf
                <button type="submit" class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-left font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">logout</span>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </div>
</div>

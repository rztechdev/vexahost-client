<x-app-layout>
    <div class="space-y-6 w-full" x-data>

            <!-- Flash Alert -->
            <x-flash />

            <!-- Title & Info Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-white tracking-tight flex items-center gap-2.5">
                        <span class="p-2 rounded-xl bg-purple-50 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400">
                            <span class="material-symbols-outlined text-[24px]">history</span>
                        </span>
                        <span>Audit Trail &amp; Activity Log</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 mt-1">Rekaman jejak aktivitas seluruh aksi tim internal dan perubahan data CRM secara real-time.</p>
                </div>
                @if($logs->total() > 0)
                    <form method="POST" action="{{ route('admin.activity-logs.destroy-all') }}" x-ref="deleteAllActivityLogs">
                        @csrf
                        @method('DELETE')
                        <button type="button" @click="VhSwal.confirmDelete('Hapus seluruh {{ $logs->total() }} log aktivitas? Data yang dihapus tidak bisa dikembalikan.', $refs.deleteAllActivityLogs)" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-rose-200 dark:border-rose-800 text-xs font-semibold bg-white dark:bg-zinc-900 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors whitespace-nowrap">
                            <span class="material-symbols-outlined text-[18px]">delete_sweep</span>
                            <span>Hapus Semua Log</span>
                        </button>
                    </form>
                @endif
            </div>

            <!-- Filter Row -->
            <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="bg-white dark:bg-zinc-900 p-4 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 shadow-sm flex flex-col md:flex-row items-center gap-3">
                <div class="relative flex-1 w-full">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 text-[20px]">search</span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari deskripsi aktivitas, nama pengguna, atau aksi..." 
                           class="w-full pl-10 pr-4 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                </div>

                <div class="flex items-center gap-2 w-full md:w-auto">
                    <select name="action" onchange="this.form.submit()" 
                            class="w-full md:w-48 px-3 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-700 dark:text-zinc-300 focus:ring-emerald-500">
                        <option value="">Semua Jenis Aksi</option>
                        @foreach($actionTypes as $actionKey => $actionLabel)
                            <option value="{{ $actionKey }}" {{ request('action') === $actionKey ? 'selected' : '' }}>{{ $actionLabel }}</option>
                        @endforeach
                    </select>

                    <select name="user_id" onchange="this.form.submit()" 
                            class="w-full md:w-44 px-3 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-700 dark:text-zinc-300 focus:ring-emerald-500">
                        <option value="">Semua Pengguna</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>

                    @if(request()->anyFilled(['q', 'action', 'user_id']))
                        <a href="{{ route('admin.activity-logs.index') }}" class="p-2 rounded-xl text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800" title="Reset Filter">
                            <span class="material-symbols-outlined text-[20px] block">restart_alt</span>
                        </a>
                    @endif
                </div>
            </form>

            <!-- Activity Logs List (Desktop: Table, Mobile: Cards) -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs overflow-hidden">
                <!-- Desktop Table (md:block) -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-zinc-50/70 dark:bg-zinc-950/50 border-b border-zinc-200/80 dark:border-zinc-800 text-[11px] font-bold font-mono uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
                            <tr>
                                <th class="px-6 py-4">Waktu</th>
                                <th class="px-6 py-4">Pengguna</th>
                                <th class="px-6 py-4">Jenis Aksi</th>
                                <th class="px-6 py-4">Deskripsi Aktivitas</th>
                                <th class="px-6 py-4">Tipe Data / ID</th>
                                <th class="px-6 py-4 text-right">IP Address</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60 text-xs">
                            @forelse($logs as $log)
                                @php
                                    $actionColor = match(true) {
                                        str_contains($log->action, 'create') || str_contains($log->action, 'convert') => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border-emerald-200/50',
                                        str_contains($log->action, 'delete') => 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400 border-rose-200/50',
                                        str_contains($log->action, 'status') => 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400 border-amber-200/50',
                                        str_contains($log->action, 'export') || str_contains($log->action, 'invoice') => 'bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-400 border-sky-200/50',
                                        default => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400',
                                    };
                                @endphp
                                <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/30 transition-colors">
                                    <td class="px-6 py-4 font-mono text-zinc-500 whitespace-nowrap">
                                        <div class="font-bold text-zinc-800 dark:text-zinc-200">{{ $log->created_at->format('d M Y') }}</div>
                                        <div class="text-[10px] text-zinc-400">{{ $log->created_at->format('H:i:s') }}</div>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-zinc-900 dark:text-white whitespace-nowrap">
                                        {{ $log->user_name ?? 'Sistem' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold tracking-wider border {{ $actionColor }}">
                                            {{ $actionTypes[$log->action] ?? ucfirst(str_replace('_', ' ', $log->action)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-zinc-700 dark:text-zinc-300 font-medium">
                                        {{ $log->description }}
                                    </td>
                                    <td class="px-6 py-4 font-mono text-zinc-400 text-[11px] whitespace-nowrap">
                                        @if($log->subject_type)
                                            <span class="font-bold text-zinc-600 dark:text-zinc-300">{{ $log->subject_type }}</span> #{{ $log->subject_id }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 font-mono text-zinc-400 text-[11px] text-right whitespace-nowrap">
                                        {{ $log->ip_address ?: '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <form method="POST" action="{{ route('admin.activity-logs.destroy', $log) }}" x-ref="deleteLogDesktop{{ $log->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" @click="VhSwal.confirmDelete('Hapus log aktivitas ini?', $refs['deleteLogDesktop{{ $log->id }}'])" class="p-1.5 rounded-lg text-zinc-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors" title="Hapus Log">
                                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-zinc-400 text-xs">
                                        Belum ada aktivitas audit log yang tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards (md:hidden) -->
                <div class="md:hidden divide-y divide-zinc-100 dark:divide-zinc-800/60">
                    @forelse($logs as $log)
                        @php
                            $actionColor = match(true) {
                                str_contains($log->action, 'create') || str_contains($log->action, 'convert') => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border-emerald-200/50',
                                str_contains($log->action, 'delete') => 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400 border-rose-200/50',
                                str_contains($log->action, 'status') => 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400 border-amber-200/50',
                                str_contains($log->action, 'export') || str_contains($log->action, 'invoice') => 'bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-400 border-sky-200/50',
                                default => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400',
                            };
                        @endphp
                        <div class="p-4 space-y-2.5">
                            <div class="flex items-center justify-between">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold tracking-wider border {{ $actionColor }}">
                                    {{ $actionTypes[$log->action] ?? ucfirst(str_replace('_', ' ', $log->action)) }}
                                </span>
                                <span class="font-mono text-[10px] text-zinc-400">{{ $log->created_at->format('d M H:i') }}</span>
                            </div>

                            <p class="text-xs text-zinc-800 dark:text-zinc-200 font-medium leading-relaxed">
                                {{ $log->description }}
                            </p>

                            <div class="flex items-center justify-between text-[11px] text-zinc-500 pt-2 border-t border-zinc-100 dark:border-zinc-800/80">
                                <span class="font-semibold">{{ $log->user_name ?? 'Sistem' }}</span>
                                <div class="flex items-center gap-2">
                                    @if($log->subject_type)
                                        <span class="font-mono text-[10px]">{{ $log->subject_type }} #{{ $log->subject_id }}</span>
                                    @endif
                                    <form method="POST" action="{{ route('admin.activity-logs.destroy', $log) }}" x-ref="deleteLogMobile{{ $log->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" @click="VhSwal.confirmDelete('Hapus log aktivitas ini?', $refs['deleteLogMobile{{ $log->id }}'])" class="p-1.5 rounded-lg text-zinc-400 hover:text-rose-600 dark:hover:text-rose-400" title="Hapus Log">
                                            <span class="material-symbols-outlined text-[16px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-zinc-400 text-xs">
                            Belum ada aktivitas audit log.
                        </div>
                    @endforelse
                </div>

                @if($logs->hasPages())
                    <div class="p-4 border-t border-zinc-200/80 dark:border-zinc-800">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>

        </div>
</x-app-layout>

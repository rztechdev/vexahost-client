<x-app-layout>
    <div class="w-full space-y-6">
            
            <!-- Header & Action -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-white tracking-tight">Daftar Riwayat Tiket</h1>
                    <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 mt-1">Pantau status permintaan perbaikan, revisi, atau penanganan kendala teknis Anda.</p>
                </div>
                <a href="{{ route('tickets.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all shadow-xs hover:shadow active:scale-95">
                    <span class="material-symbols-outlined text-[18px]">add_circle</span>
                    <span>Buat Tiket Baru</span>
                </a>
            </div>

            <!-- Alert Success -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-900/50 text-emerald-800 dark:text-emerald-300 rounded-xl flex items-start gap-3 shadow-xs">
                    <span class="material-symbols-outlined text-[20px] text-emerald-600 dark:text-emerald-400 shrink-0">check_circle</span>
                    <div>
                        <span class="font-bold text-xs">Berhasil!</span>
                        <p class="text-xs mt-0.5">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <!-- Statistics Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Total Tickets -->
                <div class="bg-white dark:bg-zinc-900 p-5 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 font-mono">Total Tiket</p>
                            <h3 class="text-2xl font-black text-zinc-900 dark:text-white mt-1">{{ $tickets->count() }}</h3>
                        </div>
                        <div class="p-2.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 rounded-xl">
                            <span class="material-symbols-outlined text-[20px]">confirmation_number</span>
                        </div>
                    </div>
                </div>

                <!-- Active Tickets -->
                <div class="bg-white dark:bg-zinc-900 p-5 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 font-mono">Tiket Aktif</p>
                            <h3 class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">
                                {{ $tickets->whereIn('status', ['open', 'pending'])->count() }}
                            </h3>
                        </div>
                        <div class="p-2.5 bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 rounded-xl">
                            <span class="material-symbols-outlined text-[20px]">pending</span>
                        </div>
                    </div>
                </div>

                <!-- Resolved Tickets -->
                <div class="bg-white dark:bg-zinc-900 p-5 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 font-mono">Tiket Selesai</p>
                            <h3 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">
                                {{ $tickets->whereIn('status', ['resolved', 'closed'])->count() }}
                            </h3>
                        </div>
                        <div class="p-2.5 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-xl">
                            <span class="material-symbols-outlined text-[20px]">task_alt</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tickets List Card -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs overflow-hidden">
                <div class="px-5 py-4 border-b border-zinc-100 dark:border-zinc-800 flex justify-between items-center">
                    <h4 class="font-bold text-zinc-900 dark:text-white text-sm">Riwayat Tiket Bantuan</h4>
                    <span class="text-[11px] text-zinc-400 dark:text-zinc-500 font-mono">Ter-update otomatis</span>
                </div>

                @if($tickets->isEmpty())
                    <!-- Empty State -->
                    <div class="py-12 px-6 text-center">
                        <div class="inline-flex p-3 bg-zinc-100 dark:bg-zinc-800 text-zinc-400 dark:text-zinc-500 rounded-xl mb-3">
                            <span class="material-symbols-outlined text-[32px]">inbox</span>
                        </div>
                        <h5 class="text-sm font-bold text-zinc-700 dark:text-zinc-300">Belum ada tiket terdaftar</h5>
                        <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1 max-w-sm mx-auto">Jika Anda mengalami kendala teknis atau membutuhkan bantuan, silakan buat tiket baru.</p>
                    </div>
                @else
                    <!-- Desktop Table (md:block) -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-zinc-50/70 dark:bg-zinc-950/50 border-b border-zinc-200/80 dark:border-zinc-800 text-[11px] font-bold font-mono uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
                                <tr>
                                    <th class="px-5 py-3.5">ID</th>
                                    <th class="px-5 py-3.5">Keluhan / Permintaan</th>
                                    <th class="px-5 py-3.5 text-center">Prioritas</th>
                                    <th class="px-5 py-3.5 text-center">SLA Deadline</th>
                                    <th class="px-5 py-3.5 text-center">Status</th>
                                    <th class="px-5 py-3.5">Penanganan</th>
                                    <th class="px-5 py-3.5">Proyek</th>
                                    <th class="px-5 py-3.5 text-right">Dibuat</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60 text-xs">
                                @foreach($tickets as $ticket)
                                    <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/30 transition-colors">
                                        <td class="px-5 py-3.5 font-mono font-bold text-zinc-400">
                                            #{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <div class="font-bold text-zinc-900 dark:text-white">{{ $ticket->title }}</div>
                                            <p class="text-zinc-400 text-[11px] mt-0.5 line-clamp-1 max-w-md">{{ $ticket->description }}</p>
                                        </td>
                                        <td class="px-5 py-3.5 text-center">
                                            @if($ticket->priority == 'high')
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400 border border-rose-200/50">High</span>
                                            @elseif($ticket->priority == 'medium')
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400 border border-amber-200/50">Medium</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200/50">Low</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 text-center font-mono text-[11px]">
                                            @if($ticket->status == 'resolved' || $ticket->status == 'closed')
                                                <span class="text-zinc-400">Selesai: {{ $ticket->resolved_at ? $ticket->resolved_at->format('d/m/y H:i') : '-' }}</span>
                                            @else
                                                @php
                                                    $slaStatus = $ticket->slaStatus();
                                                    $color = $slaStatus == 'breached' ? 'text-rose-600 font-bold' : ($slaStatus == 'warning' ? 'text-amber-600 font-bold' : 'text-emerald-600');
                                                @endphp
                                                <span class="{{ $color }}">
                                                    {{ $ticket->sla_resolution_due_at ? $ticket->sla_resolution_due_at->diffForHumans() : '-' }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 text-center">
                                            @php
                                                $statusColors = [
                                                    'open' => 'bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-300 border-sky-200/50',
                                                    'pending' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border-amber-200/50',
                                                    'resolved' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border-emerald-200/50',
                                                    'closed' => 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300',
                                                ];
                                            @endphp
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border {{ $statusColors[$ticket->status] ?? $statusColors['closed'] }}">
                                                {{ $ticket->statusLabel() }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3.5">
                                            @php $pic = $ticket->displayTechnician(); @endphp
                                            @if($pic)
                                                <div class="flex items-center gap-2">
                                                    <div class="w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 flex items-center justify-center text-[10px] font-bold">
                                                        {{ strtoupper(substr($pic->name, 0, 2)) }}
                                                    </div>
                                                    <span class="font-medium text-zinc-800 dark:text-zinc-200">{{ $pic->name }}</span>
                                                </div>
                                            @else
                                                <span class="text-zinc-400 italic text-[11px]">Menunggu PIC</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5">
                                            @if($ticket->project)
                                                <a href="{{ route('projects.show', $ticket->project) }}" class="font-bold text-emerald-600 hover:underline">
                                                    {{ $ticket->project->name }}
                                                </a>
                                            @else
                                                <span class="text-zinc-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 text-right font-mono text-zinc-400 text-[11px]">
                                            {{ $ticket->created_at->format('d/m/Y') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Cards (md:hidden) -->
                    <div class="md:hidden divide-y divide-zinc-100 dark:divide-zinc-800/60">
                        @foreach($tickets as $ticket)
                            <div class="p-4 space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="font-mono text-[10px] text-zinc-400 font-bold">#{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $statusColors[$ticket->status] ?? 'bg-zinc-100 text-zinc-700' }}">
                                        {{ $ticket->statusLabel() }}
                                    </span>
                                </div>

                                <div>
                                    <h5 class="font-bold text-xs text-zinc-900 dark:text-white">{{ $ticket->title }}</h5>
                                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-1 line-clamp-2 leading-relaxed">{{ $ticket->description }}</p>
                                </div>

                                <div class="flex items-center justify-between pt-2 border-t border-zinc-100 dark:border-zinc-800/80 text-[10px]">
                                    <div class="flex items-center gap-1.5">
                                        @if($ticket->priority == 'high')
                                            <span class="px-1.5 py-0.5 rounded bg-rose-50 text-rose-700 font-bold">High</span>
                                        @elseif($ticket->priority == 'medium')
                                            <span class="px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 font-bold">Med</span>
                                        @else
                                            <span class="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 font-bold">Low</span>
                                        @endif

                                        @if($ticket->project)
                                            <span class="text-zinc-500">• {{ $ticket->project->name }}</span>
                                        @endif
                                    </div>
                                    <span class="font-mono text-zinc-400">{{ $ticket->created_at->format('d M Y') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
</x-app-layout>



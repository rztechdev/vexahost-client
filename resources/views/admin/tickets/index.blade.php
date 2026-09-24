<x-app-layout>
    <div class="w-full space-y-6">

            <!-- Title Header -->
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-white tracking-tight">Manajemen Tiket &amp; Proyek</h1>
                <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 mt-1">Kelola seluruh tiket kendala klien dan delegasikan / konversi menjadi proyek kerja tim.</p>
            </div>

            <!-- Statistics Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Total System Tickets -->
                <div class="bg-white dark:bg-zinc-900 p-5 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Semua Tiket Masuk</p>
                            <h3 class="text-2xl font-black text-zinc-900 dark:text-white mt-1">
                                {{ \App\Models\Ticket::count() }}
                            </h3>
                        </div>
                        <div class="p-2.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 rounded-xl">
                            <span class="material-symbols-outlined text-[20px]">inbox</span>
                        </div>
                    </div>
                </div>

                <!-- Unassigned Tickets -->
                <div class="bg-white dark:bg-zinc-900 p-5 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Belum Ada Proyek</p>
                            <h3 class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-1">
                                {{ \App\Models\Ticket::doesntHave('project')->count() }}
                            </h3>
                        </div>
                        <div class="p-2.5 bg-rose-50 dark:bg-rose-950/40 text-rose-600 rounded-xl">
                            <span class="material-symbols-outlined text-[20px]">warning</span>
                        </div>
                    </div>
                </div>

                <!-- Active Projects -->
                <div class="bg-white dark:bg-zinc-900 p-5 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-mono font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Proyek Berjalan</p>
                            <h3 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">
                                {{ \App\Models\Project::whereWorkStatus('active')->count() }}
                            </h3>
                        </div>
                        <div class="p-2.5 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 rounded-xl">
                            <span class="material-symbols-outlined text-[20px]">view_kanban</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- List Table Card -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs overflow-hidden">
                <div class="px-5 py-4 border-b border-zinc-100 dark:border-zinc-800 flex justify-between items-center">
                    <h3 class="font-bold text-sm text-zinc-900 dark:text-white">Daftar Semua Tiket Klien</h3>
                    <span class="text-xs text-zinc-400 font-mono">Total: {{ $tickets->total() }} tiket</span>
                </div>

                @if($tickets->isEmpty())
                    <div class="py-12 px-6 text-center">
                        <div class="inline-flex p-3 bg-zinc-100 dark:bg-zinc-800 text-zinc-400 dark:text-zinc-500 rounded-xl mb-3">
                            <span class="material-symbols-outlined text-[32px]">inbox</span>
                        </div>
                        <h5 class="text-sm font-bold text-zinc-700 dark:text-zinc-300">Belum ada tiket masuk</h5>
                    </div>
                @else
                    <!-- Desktop Table (md:block) -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="bg-zinc-50/70 dark:bg-zinc-950/50 border-b border-zinc-200/80 dark:border-zinc-800 text-zinc-400 dark:text-zinc-500 font-mono text-[11px] uppercase tracking-wider font-semibold">
                                    <th class="px-5 py-3.5">ID</th>
                                    <th class="px-5 py-3.5">Klien</th>
                                    <th class="px-5 py-3.5">Keluhan</th>
                                    <th class="px-5 py-3.5 text-center">Prioritas</th>
                                    <th class="px-5 py-3.5 text-center">SLA Respons</th>
                                    <th class="px-5 py-3.5 text-center">Status</th>
                                    <th class="px-5 py-3.5 text-center">Hubungan Proyek</th>
                                    <th class="px-5 py-3.5 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 text-xs">
                                @foreach($tickets as $ticket)
                                    <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/30 transition-colors">
                                        <td class="px-5 py-3.5 font-mono font-bold text-zinc-400">
                                            #{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-7 h-7 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 flex items-center justify-center text-[10px] font-bold">
                                                    {{ strtoupper(substr($ticket->client->name, 0, 2)) }}
                                                </div>
                                                <div>
                                                    <span class="font-bold text-zinc-900 dark:text-zinc-100">{{ $ticket->client->name }}</span>
                                                    <p class="text-[10px] font-mono text-zinc-400">{{ $ticket->client->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <div class="font-bold text-zinc-900 dark:text-zinc-100">{{ $ticket->title }}</div>
                                            <p class="text-[11px] text-zinc-400 mt-0.5 line-clamp-1 max-w-xs">{{ $ticket->description }}</p>
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
                                            @if($ticket->first_response_at)
                                                <span class="font-bold text-emerald-600 dark:text-emerald-400">
                                                    OK ({{ $ticket->first_response_at->format('H:i') }})
                                                </span>
                                            @else
                                                @php
                                                    $respBreached = $ticket->isResponseSlaBreached();
                                                    $respColor = $respBreached ? 'text-rose-600 font-bold' : 'text-emerald-600';
                                                @endphp
                                                <span class="{{ $respColor }}">
                                                    {{ $ticket->sla_response_due_at ? $ticket->sla_response_due_at->diffForHumans() : '-' }}
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
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border {{ $statusColors[$ticket->status] ?? 'bg-zinc-100 text-zinc-700' }}">
                                                {{ $ticket->statusLabel() }}
                                            </span>
                                        </td>

                                        <!-- Hubungan Proyek -->
                                        <td class="px-5 py-3.5 text-center">
                                            @if($ticket->project)
                                                <a href="{{ route('projects.show', $ticket->project) }}" class="inline-flex items-center gap-1 text-xs text-emerald-600 font-bold hover:underline">
                                                    <span>{{ $ticket->project->name }}</span>
                                                </a>
                                            @else
                                                <span class="text-zinc-400 text-xs">Belum ada</span>
                                            @endif
                                        </td>

                                        <!-- Aksi -->
                                        <td class="px-5 py-3.5 text-right">
                                            @if(!$ticket->project)
                                                <a href="{{ route('projects.create', ['ticket_id' => $ticket->id]) }}" 
                                                   class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition active:scale-95">
                                                    Buat Proyek
                                                </a>
                                            @else
                                                <a href="{{ route('projects.show', $ticket->project) }}" 
                                                   class="px-2.5 py-1 bg-zinc-100 dark:bg-zinc-800 hover:bg-emerald-600 hover:text-white dark:hover:bg-emerald-600 dark:hover:text-white text-zinc-700 dark:text-zinc-300 font-bold text-xs rounded-xl transition-colors">
                                                    Detail
                                                </a>
                                            @endif
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
                                    <div class="text-xs font-bold text-zinc-900 dark:text-white">{{ $ticket->title }}</div>
                                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-1 line-clamp-2 leading-relaxed">{{ $ticket->description }}</p>
                                </div>

                                <div class="flex items-center justify-between pt-2 border-t border-zinc-100 dark:border-zinc-800/80 text-[11px]">
                                    <span class="text-zinc-500">Klien: <b>{{ $ticket->client->name }}</b></span>
                                    @if($ticket->project)
                                        <a href="{{ route('projects.show', $ticket->project) }}" class="px-2.5 py-1 rounded bg-zinc-100 dark:bg-zinc-800 text-emerald-600 font-bold text-[11px]">
                                            Detail Proyek
                                        </a>
                                    @else
                                        <a href="{{ route('projects.create', ['ticket_id' => $ticket->id]) }}" class="px-2.5 py-1 rounded bg-emerald-600 text-white font-bold text-[11px]">
                                            + Buat Proyek
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($tickets->hasPages())
                        <div class="px-5 py-4 border-t border-zinc-100 dark:border-zinc-800">
                            {{ $tickets->links() }}
                        </div>
                    @endif
                @endif
            </div>

        </div>
</x-app-layout>



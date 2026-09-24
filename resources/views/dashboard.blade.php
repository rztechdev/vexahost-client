<x-app-layout>
    <div class="w-full space-y-6">

        <!-- Header Title Row -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-white tracking-tight">Dashboard</h1>
                <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 mt-1">Pantau perkembangan proyek website, penanganan tiket teknis, dan performa SLA layanan.</p>
            </div>
            <div class="flex items-center gap-2.5">
                @can('tickets.create')
                    <a href="{{ route('tickets.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-600 dark:hover:bg-emerald-500 text-white text-xs font-bold transition-all duration-200 shadow-sm hover:shadow active:scale-95">
                        <span class="material-symbols-outlined text-[18px]">add_circle</span>
                        <span>Buat Tiket Baru</span>
                    </a>
                @endcan
                <a href="{{ route('projects.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs font-semibold transition-all duration-200 shadow-xs">
                    <span class="material-symbols-outlined text-[18px]">view_kanban</span>
                    <span>Lihat Proyek</span>
                </a>
            </div>
        </div>

        @php
            $isClient = auth()->user()->hasRole('client');
            $isAdmin = auth()->user()->hasRole('admin') || auth()->user()->can('invoices.manage');
        @endphp

        <!-- Invoice Banner (Differentiated for Client vs Admin) -->
        @if(isset($invoice_summary) && $invoice_summary['unpaid_count'] > 0)
            @if($isAdmin)
                <div class="p-4 rounded-xl bg-zinc-900 border border-zinc-800 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[24px]">account_balance_wallet</span>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-white flex items-center gap-2">
                                <span>Monitoring Piutang: Terdapat {{ $invoice_summary['unpaid_count'] }} tagihan klien aktif belum lunas</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono bg-emerald-950 text-emerald-300 border border-emerald-800/60 font-bold">
                                    Total Piutang: Rp {{ number_format($invoice_summary['total_due'], 0, ',', '.') }}
                                </span>
                            </div>
                            <p class="text-[11px] text-zinc-400 mt-0.5">
                                Pantau mutasi rekening kas masuk dan lakukan verifikasi pembayaran klien secara berkala.
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('invoices.index') }}" class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition-all shadow-xs">
                            <span class="material-symbols-outlined text-[16px]">verified</span>
                            <span>Kelola &amp; Verifikasi Tagihan</span>
                        </a>
                    </div>
                </div>
            @else
                <div class="p-4 rounded-xl bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-transparent border border-amber-500/30 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-xs">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[24px]">payments</span>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-zinc-900 dark:text-white">
                                Anda memiliki {{ $invoice_summary['unpaid_count'] }} tagihan proyek yang belum lunas (Sisa: Rp {{ number_format($invoice_summary['total_due'], 0, ',', '.') }})
                            </div>
                            <p class="text-[11px] text-zinc-500 dark:text-zinc-400">
                                Pembayaran dapat dilakukan via Transfer Bank atau Scan QRIS resmi PT DESTINARA CHAKRAWALA ARTHA.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('invoices.index') }}" class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold transition-all shadow-xs shrink-0">
                        <span>Lihat &amp; Bayar Tagihan</span>
                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                    </a>
                </div>
            @endif
        @endif

        {{-- Subscription Expiry Warnings --}}
        @if(!empty($expiring_subscriptions))
            @foreach($expiring_subscriptions as $sub)
                <div class="p-4 rounded-xl {{ $sub['status'] === 'expired' ? 'bg-gradient-to-r from-red-500/10 via-red-500/5 to-transparent border border-red-500/30' : 'bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-transparent border border-amber-500/30' }} flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-xs">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl {{ $sub['status'] === 'expired' ? 'bg-red-500/20 text-red-600 dark:text-red-400' : 'bg-amber-500/20 text-amber-600 dark:text-amber-400' }} flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[24px]">{{ $sub['status'] === 'expired' ? 'error' : 'schedule' }}</span>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                                <span>{{ $sub['status'] === 'expired' ? 'Masa berlaku' : 'Masa berlaku akan segera berakhir:' }} <strong>{{ $sub['name'] }}</strong></span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-{{ $sub['color'] }}-100 text-{{ $sub['color'] }}-800 dark:bg-{{ $sub['color'] }}-950 dark:text-{{ $sub['color'] }}-300 border border-{{ $sub['color'] }}-200/50">
                                    {{ $sub['label'] }}
                                </span>
                            </div>
                            <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5">
                                @if($sub['status'] === 'expired')
                                    Expired sejak {{ $sub['expired'] }}. Segera perpanjang agar layanan tetap aktif.
                                @else
                                    Expired {{ $sub['expired'] }} ({{ $sub['sisa_hari'] }} hari lagi) • Biaya: Rp {{ number_format($sub['price'], 0, ',', '.') }}
                                @endif
                            </p>
                        </div>
                    </div>
                    <a href="{{ $sub['url'] }}" class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-lg {{ $sub['status'] === 'expired' ? 'bg-red-600 hover:bg-red-700' : 'bg-amber-600 hover:bg-amber-700' }} text-white text-xs font-bold transition-all shadow-xs shrink-0">
                        <span>Lihat Detail</span>
                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                    </a>
                </div>
            @endforeach
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-zinc-900 p-5 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-zinc-400 dark:text-zinc-500 text-[11px] font-mono font-bold uppercase tracking-wider">Proyek Aktif</p>
                            <h4 class="text-2xl font-black text-zinc-900 dark:text-white mt-1">{{ $stats['active_projects'] }}</h4>
                        </div>
                        <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-emerald-600 dark:text-emerald-400">
                            <span class="material-symbols-outlined text-[20px]">view_kanban</span>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center text-xs {{ $stats['projects_trend']['positive'] ? 'text-emerald-600 dark:text-emerald-400 font-bold' : 'text-rose-600 dark:text-rose-400' }}">
                        <span class="material-symbols-outlined text-sm mr-1">{{ $stats['projects_trend']['positive'] ? 'trending_up' : 'trending_down' }}</span>
                        {{ $stats['projects_trend']['label'] }}
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-900 p-5 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-zinc-400 dark:text-zinc-500 text-[11px] font-mono font-bold uppercase tracking-wider">Tiket Terbuka</p>
                            <h4 class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ $stats['open_tickets'] }}</h4>
                        </div>
                        <div class="p-2 bg-amber-50 dark:bg-amber-950/40 rounded-lg text-amber-600">
                            <span class="material-symbols-outlined text-[20px]">confirmation_number</span>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center text-xs text-rose-600 dark:text-rose-400">
                        @if($stats['sla_warning_count'] > 0)
                            <span class="material-symbols-outlined text-sm mr-1">warning</span>
                            <span class="font-bold">{{ $stats['sla_warning_count'] }} tiket mendekati SLA</span>
                        @else
                            <span class="material-symbols-outlined text-sm mr-1 text-emerald-500">check_circle</span>
                            <span class="text-emerald-600 dark:text-emerald-400 font-medium">SLA Terkendali</span>
                        @endif
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-900 p-5 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-zinc-400 dark:text-zinc-500 text-[11px] font-mono font-bold uppercase tracking-wider">SLA Compliance</p>
                            <h4 class="text-2xl font-black text-zinc-900 dark:text-white mt-1">{{ $stats['sla_compliance_percent'] }}%</h4>
                        </div>
                        <div class="p-2 bg-emerald-50 dark:bg-emerald-950/40 rounded-lg text-emerald-600">
                            <span class="material-symbols-outlined text-[20px]">verified</span>
                        </div>
                    </div>
                    <div class="mt-3 w-full bg-zinc-100 dark:bg-zinc-800 h-1.5 rounded-full overflow-hidden">
                        <div class="bg-emerald-600 h-1.5 rounded-full transition-all duration-500" style="width: {{ $stats['sla_compliance_percent'] }}%"></div>
                    </div>
                    @if($stats['sla_tracked_count'] > 0)
                        <p class="mt-1.5 text-[10px] font-mono text-zinc-400 dark:text-zinc-500">{{ $stats['sla_tracked_count'] }} tiket SLA aktif</p>
                    @endif
                </div>

                <div class="bg-white dark:bg-zinc-900 p-5 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-zinc-400 dark:text-zinc-500 text-[11px] font-mono font-bold uppercase tracking-wider">Total Tiket</p>
                            <h4 class="text-2xl font-black text-zinc-900 dark:text-white mt-1">{{ $stats['total_tickets'] }}</h4>
                        </div>
                        <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-zinc-600 dark:text-zinc-300">
                            <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                        </div>
                    </div>
                    <div class="mt-3 text-[11px] text-zinc-400">Total riwayat layanan</div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-1 bg-white dark:bg-zinc-900 p-5 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs">
                    <h4 class="text-zinc-900 dark:text-white font-bold text-sm mb-1 flex items-center gap-2">
                        <span class="material-symbols-outlined text-emerald-600 text-[18px]">pie_chart</span>
                        <span>Tiket Per Status</span>
                    </h4>
                    <p class="text-[11px] text-zinc-400 mb-3">Distribusi status tiket</p>
                    <div class="relative h-60 flex items-center justify-center">
                        <canvas id="ticketsChart"></canvas>
                    </div>
                </div>

                <div class="lg:col-span-1 bg-white dark:bg-zinc-900 p-5 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs">
                    <h4 class="text-zinc-900 dark:text-white font-bold text-sm mb-1 flex items-center gap-2">
                        <span class="material-symbols-outlined text-emerald-600 text-[18px]">donut_large</span>
                        <span>Tugas Tim</span>
                    </h4>
                    <p class="text-[11px] text-zinc-400 mb-3">Status tugas proyek</p>
                    <div class="relative h-60 flex items-center justify-center">
                        <canvas id="tasksChart"></canvas>
                    </div>
                </div>

                <div class="lg:col-span-1 bg-white dark:bg-zinc-900 p-5 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs">
                    <h4 class="text-zinc-900 dark:text-white font-bold text-sm mb-1 flex items-center gap-2">
                        <span class="material-symbols-outlined text-emerald-600 text-[18px]">bar_chart</span>
                        <span>Status Proyek</span>
                    </h4>
                    <p class="text-[11px] text-zinc-400 mb-3">Agregasi status proyek</p>
                    <div class="relative h-60">
                        <canvas id="projectsChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Card (Desktop Table + Mobile Cards) -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-zinc-100 dark:border-zinc-800 flex justify-between items-center">
                    <h4 class="text-zinc-900 dark:text-white font-bold text-sm">Aktivitas Terkini</h4>
                    <a href="{{ route('projects.index') }}" class="text-xs text-emerald-600 hover:underline font-bold">Lihat Proyek</a>
                </div>

                <!-- Desktop Table (md:block) -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left text-sm text-zinc-700 dark:text-zinc-300">
                        <thead>
                            <tr class="bg-zinc-50/70 dark:bg-zinc-950/50 border-b border-zinc-200/80 dark:border-zinc-800 text-zinc-400 dark:text-zinc-500 font-mono uppercase text-[10px] tracking-wider">
                                <th class="px-5 py-3">Proyek / Tiket</th>
                                <th class="px-5 py-3">Assignee</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3 text-right">Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60 text-xs">
                            @forelse($recent_activity as $row)
                                @php
                                    $statusColors = [
                                        'open' => 'bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-300 border border-sky-200/50',
                                        'pending' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200/50',
                                        'resolved' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200/50',
                                        'closed' => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300',
                                        'active' => 'bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-300 border border-sky-200/50',
                                        'completed' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200/50',
                                        'todo' => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300',
                                        'in_progress' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200/50',
                                        'done' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200/50',
                                    ];
                                    $badgeClass = $statusColors[$row['status']] ?? 'bg-zinc-100 text-zinc-700';
                                @endphp
                                <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/30 transition-colors">
                                    <td class="px-5 py-3 font-medium text-zinc-900 dark:text-zinc-100">
                                        @if($row['url'])
                                            <a href="{{ $row['url'] }}" class="hover:text-emerald-600 transition-colors font-bold">{{ $row['title'] }}</a>
                                        @else
                                            <span class="font-bold">{{ $row['title'] }}</span>
                                        @endif
                                        <span class="ml-2 text-[10px] text-zinc-400 uppercase font-mono">{{ $row['type'] }}</span>
                                    </td>
                                    <td class="px-5 py-3">{{ $row['assignee'] }}</td>
                                    <td class="px-5 py-3">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $badgeClass }}">{{ $row['status_label'] }}</span>
                                    </td>
                                    <td class="px-5 py-3 text-right text-zinc-400 font-mono text-[11px]">{{ $row['time_ago'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-8 text-center text-zinc-400 text-xs">
                                        Belum ada aktivitas baru.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards (md:hidden) -->
                <div class="md:hidden divide-y divide-zinc-100 dark:divide-zinc-800/60">
                    @forelse($recent_activity as $row)
                        @php
                            $statusColors = [
                                'open' => 'bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-300 border border-sky-200/50',
                                'pending' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200/50',
                                'resolved' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200/50',
                                'closed' => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300',
                                'active' => 'bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-300 border border-sky-200/50',
                                'completed' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200/50',
                                'todo' => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300',
                                'in_progress' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200/50',
                                'done' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200/50',
                            ];
                            $badgeClass = $statusColors[$row['status']] ?? 'bg-zinc-100 text-zinc-700';
                        @endphp
                        <div class="p-4 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $badgeClass }}">
                                    {{ $row['status_label'] }}
                                </span>
                                <span class="font-mono text-[10px] text-zinc-400">{{ $row['time_ago'] }}</span>
                            </div>

                            <div>
                                @if($row['url'])
                                    <a href="{{ $row['url'] }}" class="font-bold text-xs text-zinc-900 dark:text-white hover:text-emerald-600">
                                        {{ $row['title'] }}
                                    </a>
                                @else
                                    <span class="font-bold text-xs text-zinc-900 dark:text-white">{{ $row['title'] }}</span>
                                @endif
                                <span class="ml-1 text-[10px] font-mono text-zinc-400 uppercase">({{ $row['type'] }})</span>
                            </div>

                            <div class="text-[11px] text-zinc-500 pt-1 border-t border-zinc-100 dark:border-zinc-800/80">
                                Assignee: <span class="font-semibold text-zinc-700 dark:text-zinc-300">{{ $row['assignee'] }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-zinc-400 text-xs">
                            Belum ada aktivitas baru.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    @push('scripts')
    <script id="dashboard-data" type="application/json">
        @json($charts)
    </script>
    @endpush
</x-app-layout>


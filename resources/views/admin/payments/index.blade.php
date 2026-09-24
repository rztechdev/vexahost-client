<x-app-layout>
    <div class="space-y-6 w-full">

            <!-- Flash Alert -->
            <x-flash />

            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-white tracking-tight">Pembayaran &amp; Invoicing</h1>
                    <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 mt-1">Daftar transaksi DP proyek, pelunasan akhir, dan pembayaran maintenance klien.</p>
                </div>
                <div class="relative" x-data="{ openExport: false }">
                    <button @click="openExport = !openExport" @click.outside="openExport = false" 
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 hover:border-zinc-300 text-zinc-700 dark:text-zinc-300 text-xs font-semibold transition-all shadow-xs">
                        <span class="material-symbols-outlined text-[16px] text-sky-600">download</span>
                        <span>Export Pembayaran</span>
                        <span class="material-symbols-outlined text-[14px] text-zinc-400">expand_more</span>
                    </button>
                    <div x-show="openExport" x-cloak 
                         class="absolute right-0 mt-1.5 w-44 bg-white dark:bg-zinc-900 rounded-lg shadow-lg border border-zinc-200 dark:border-zinc-800 py-1 z-50 text-xs font-medium">
                        <a href="{{ route('admin.export.payments', array_merge(request()->query(), ['format' => 'csv'])) }}" 
                           class="flex items-center gap-2 px-3 py-2 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                            <span class="material-symbols-outlined text-[16px] text-emerald-600">table_chart</span>
                            <span>Export CSV / Excel</span>
                        </a>
                        <a href="{{ route('admin.export.payments', array_merge(request()->query(), ['format' => 'pdf'])) }}" target="_blank"
                           class="flex items-center gap-2 px-3 py-2 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                            <span class="material-symbols-outlined text-[16px] text-rose-600">picture_as_pdf</span>
                            <span>Export PDF</span>
                        </a>
                        <a href="{{ route('admin.export.payments', array_merge(request()->query(), ['format' => 'word'])) }}" 
                           class="flex items-center gap-2 px-3 py-2 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                            <span class="material-symbols-outlined text-[16px] text-blue-600">description</span>
                            <span>Export Word (.doc)</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-zinc-400 font-mono">Total Pembayaran Lunas</p>
                        <h3 class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-1">Rp {{ number_format($totalLunas, 0, ',', '.') }}</h3>
                    </div>
                    <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400">
                        <span class="material-symbols-outlined text-[28px]">verified</span>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-zinc-400 font-mono">Total Tagihan Pending</p>
                        <h3 class="text-2xl sm:text-3xl font-black text-amber-500 mt-1">Rp {{ number_format($totalPending, 0, ',', '.') }}</h3>
                    </div>
                    <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-500">
                        <span class="material-symbols-outlined text-[28px]">pending</span>
                    </div>
                </div>
            </div>

            <!-- Payments Table Card (Desktop: Table, Mobile: Cards) -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs overflow-hidden">
                <!-- Desktop Table (md:block) -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-zinc-50/70 dark:bg-zinc-950/50 border-b border-zinc-200/80 dark:border-zinc-800 text-[11px] font-bold font-mono uppercase tracking-wider text-zinc-400 dark:text-zinc-500">
                            <tr>
                                <th class="px-6 py-4">Proyek &amp; Klien</th>
                                <th class="px-6 py-4">Jenis</th>
                                <th class="px-6 py-4">Nominal</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Tanggal</th>
                                <th class="px-6 py-4">Catatan</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60 text-xs">
                            @forelse($payments as $payment)
                                <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <a href="{{ route('admin.projects.show', $payment->project) }}" class="font-bold text-zinc-900 dark:text-white hover:text-emerald-600">
                                            {{ $payment->project->nama_project }}
                                        </a>
                                        <p class="text-zinc-400 text-[11px] mt-0.5">{{ $payment->project->lead->nama_usaha }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-semibold text-[11px]">
                                            {{ $payment->jenis_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-mono font-bold text-zinc-900 dark:text-white">
                                        Rp {{ number_format($payment->jumlah, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider {{ $payment->status === 'lunas' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400' : 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400' }}">
                                            {{ strtoupper($payment->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-mono text-zinc-500">
                                        {{ $payment->tanggal ? $payment->tanggal->format('d M Y') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-zinc-500 truncate max-w-xs">
                                        {{ $payment->catatan ?: '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('admin.invoices.receipt', $payment) }}" target="_blank" class="p-1.5 rounded-lg text-zinc-400 hover:text-emerald-600 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors" title="Cetak Kwitansi Resmi">
                                                <span class="material-symbols-outlined text-[18px]">receipt</span>
                                            </a>

                                            @if($payment->status === 'pending')
                                                <form method="POST" action="{{ route('admin.payments.update-status', $payment) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="lunas">
                                                    <button type="submit" class="px-2.5 py-1 rounded bg-emerald-600 text-white text-[11px] font-bold hover:bg-emerald-700">
                                                        Tandai Lunas
                                                    </button>
                                                </form>
                                            @endif
                                            <form method="POST" action="{{ route('admin.payments.destroy', $payment) }}" x-ref="deletePaymentDesktop{{ $payment->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" @click="VhSwal.confirmDelete('Hapus pembayaran ini?', $refs['deletePaymentDesktop{{ $payment->id }}'])" class="p-1.5 rounded-lg text-zinc-400 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Hapus Pembayaran">
                                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-zinc-400">
                                        Belum ada data pembayaran transaksi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards (md:hidden) -->
                <div class="md:hidden divide-y divide-zinc-100 dark:divide-zinc-800/60">
                    @forelse($payments as $payment)
                        <div class="p-4 space-y-3">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <a href="{{ route('admin.projects.show', $payment->project) }}" class="font-bold text-xs text-zinc-900 dark:text-white">
                                        {{ $payment->project->nama_project }}
                                    </a>
                                    <p class="text-[11px] text-zinc-500 mt-0.5">{{ $payment->project->lead->nama_usaha }}</p>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $payment->status === 'lunas' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400' : 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400' }}">
                                    {{ strtoupper($payment->status) }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between text-xs">
                                <span class="px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-semibold text-[10px]">
                                    {{ $payment->jenis_label }}
                                </span>
                                <span class="font-mono font-bold text-xs text-zinc-900 dark:text-white">
                                    Rp {{ number_format($payment->jumlah, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between pt-2 border-t border-zinc-100 dark:border-zinc-800/80 text-[11px] text-zinc-500">
                                <span class="font-mono">{{ $payment->tanggal ? $payment->tanggal->format('d M Y') : '-' }}</span>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.invoices.receipt', $payment) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-semibold text-[11px]">
                                        <span class="material-symbols-outlined text-[14px]">receipt</span>
                                        <span>Kwitansi</span>
                                    </a>
                                    @if($payment->status === 'pending')
                                        <form method="POST" action="{{ route('admin.payments.update-status', $payment) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="lunas">
                                            <button type="submit" class="px-2.5 py-1 rounded bg-emerald-600 text-white text-[11px] font-bold">
                                                Lunas
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-zinc-400 text-xs">
                            Belum ada data pembayaran transaksi.
                        </div>
                    @endforelse
                </div>

                @if($payments->hasPages())
                    <div class="p-4 border-t border-zinc-200/80 dark:border-zinc-800">
                        {{ $payments->links() }}
                    </div>
                @endif
            </div>

        </div>
</x-app-layout>

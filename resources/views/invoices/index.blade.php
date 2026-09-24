<x-app-layout>
    <div class="w-full space-y-6">
        
        <!-- Header & Description -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                @if($isAdmin)
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-zinc-900 text-white dark:bg-white dark:text-zinc-900 mb-1.5 shadow-2xs">
                        <span class="material-symbols-outlined text-[13px] text-emerald-400">admin_panel_settings</span>
                        <span>Mode Administrator / Finance Desk</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-white tracking-tight">
                        Manajemen Tagihan Seluruh Klien
                    </h1>
                    <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                        Monitoring arus kas masuk, piutang invoice proyek, dan persetujuan verifikasi transfer seluruh klien PT DESTINARA CHAKRAWALA ARTHA.
                    </p>
                @else
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-zinc-900 dark:text-white tracking-tight">
                        Tagihan &amp; Pembayaran Proyek Saya
                    </h1>
                    <p class="text-xs sm:text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                        Pantau rincian biaya proyek, riwayat pembayaran uang muka/pelunasan, serta konfirmasi transfer secara resmi.
                    </p>
                @endif
            </div>
            <div class="flex items-center gap-2">
                @if($isAdmin)
                    <a href="https://client.vexahostcloud.my.id/admin/projects" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700 text-xs font-bold transition-all">
                        <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                        <span>Buka CRM Proyek</span>
                    </a>
                @else
                    <a href="https://wa.me/6285808749131?text={{ rawurlencode('Halo Tim Finance VexaHost, saya ingin menanyakan perihal tagihan proyek saya.') }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-600/10 hover:bg-emerald-600/20 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20 text-xs font-bold transition-all">
                        <span class="material-symbols-outlined text-[18px]">chat</span>
                        <span>Bantuan Finance via WA</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Alert Notifications -->
        @if(session('success'))
            <div class="p-4 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-900/50 text-emerald-800 dark:text-emerald-300 rounded-xl flex items-start gap-3 shadow-xs">
                <span class="material-symbols-outlined text-[20px] text-emerald-600 dark:text-emerald-400 shrink-0">check_circle</span>
                <div>
                    <span class="font-bold text-xs">Berhasil!</span>
                    <p class="text-xs mt-0.5">{{ session('success') }}</p>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/50 text-rose-800 dark:text-rose-300 rounded-xl flex items-start gap-3 shadow-xs">
                <span class="material-symbols-outlined text-[20px] text-rose-600 dark:text-rose-400 shrink-0">error</span>
                <div>
                    <span class="font-bold text-xs">Perhatian!</span>
                    <p class="text-xs mt-0.5">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Financial Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Nilai Tagihan -->
            <div class="bg-white dark:bg-zinc-900 p-5 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 font-mono">
                            {{ $isAdmin ? 'Total Portofolio Tagihan' : 'Total Nilai Tagihan' }}
                        </p>
                        <h3 class="text-xl sm:text-2xl font-black text-zinc-900 dark:text-white mt-1">
                            Rp {{ number_format($stats['total_amount'], 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="p-2.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 rounded-xl">
                        <span class="material-symbols-outlined text-[20px]">account_balance_wallet</span>
                    </div>
                </div>
            </div>

            <!-- Total Telah Terbayar -->
            <div class="bg-white dark:bg-zinc-900 p-5 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 font-mono">
                            {{ $isAdmin ? 'Kas Masuk Terverifikasi' : 'Total Terbayar (DP/Mutasi)' }}
                        </p>
                        <h3 class="text-xl sm:text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">
                            Rp {{ number_format($stats['total_paid'], 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="p-2.5 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-xl">
                        <span class="material-symbols-outlined text-[20px]">check_circle</span>
                    </div>
                </div>
            </div>

            <!-- Sisa Tagihan (Due) -->
            <div class="bg-white dark:bg-zinc-900 p-5 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 font-mono">
                            {{ $isAdmin ? 'Piutang Berjalan (Due)' : 'Sisa Tagihan (Due)' }}
                        </p>
                        <h3 class="text-xl sm:text-2xl font-black {{ $stats['total_due'] > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-zinc-900 dark:text-white' }} mt-1">
                            Rp {{ number_format($stats['total_due'], 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="p-2.5 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 rounded-xl">
                        <span class="material-symbols-outlined text-[20px]">pending_actions</span>
                    </div>
                </div>
            </div>

            <!-- Status Verifikasi -->
            <div class="bg-white dark:bg-zinc-900 p-5 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 font-mono">
                            {{ $isAdmin ? 'Menunggu Verifikasi Finance' : 'Status Verifikasi' }}
                        </p>
                        <h3 class="text-xl sm:text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">
                            {{ $stats['verifying_count'] }} Dokumen
                        </h3>
                    </div>
                    <div class="p-2.5 bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 rounded-xl">
                        <span class="material-symbols-outlined text-[20px]">hourglass_top</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoices Table Container -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-zinc-200/80 dark:border-zinc-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-zinc-900 dark:text-white">
                        {{ $isAdmin ? 'Daftar Rekapitulasi Tagihan Klien' : 'Daftar Faktur Tagihan' }}
                    </h2>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                        {{ $isAdmin ? 'Pilih invoice untuk memverifikasi pembayaran masuk, memeriksa bukti transfer, atau mengunduh PDF.' : 'Pilih tagihan untuk melihat detail rekening resmi, barcode QRIS, atau upload bukti transfer.' }}
                    </p>
                </div>
                
                <!-- Filter Form -->
                <form action="{{ route('invoices.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                    <div class="relative">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ $isAdmin ? 'Cari no. invoice, klien, proyek...' : 'Cari nomor invoice...' }}" class="text-xs rounded-xl border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-zinc-900 dark:text-white pl-8 pr-3 py-1.5 focus:ring-emerald-500 focus:border-emerald-500 w-44 sm:w-56">
                        <span class="material-symbols-outlined absolute left-2.5 top-2 text-[16px] text-zinc-400">search</span>
                    </div>

                    <select name="status" onchange="this.form.submit()" class="text-xs rounded-xl border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-zinc-900 dark:text-white py-1.5 pl-3 pr-8 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Semua Status</option>
                        <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                        <option value="partially_paid" {{ request('status') === 'partially_paid' ? 'selected' : '' }}>DP Diterima</option>
                        <option value="verifying" {{ request('status') === 'verifying' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Lunas</option>
                    </select>

                    @if(request('q') || request('status'))
                        <a href="{{ route('invoices.index') }}" class="p-1.5 rounded-lg text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors" title="Reset Filter">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </a>
                    @endif
                </form>
            </div>

            <!-- Invoices Table -->
            @if($invoices->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-12 h-12 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-400 flex items-center justify-center mx-auto mb-3">
                        <span class="material-symbols-outlined text-[24px]">receipt_long</span>
                    </div>
                    <h3 class="text-sm font-bold text-zinc-800 dark:text-zinc-200">Tidak ada tagihan yang ditemukan</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                        {{ request('q') || request('status') ? 'Coba ubah kata kunci atau reset filter status Anda.' : 'Belum ada data tagihan proyek yang terdaftar.' }}
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-zinc-200/80 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30 text-[11px] font-mono font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                <th class="py-3 px-4">No. Invoice</th>
                                @if($isAdmin)
                                    <th class="py-3 px-4">Klien / Kontak</th>
                                @endif
                                <th class="py-3 px-4">Proyek Layanan</th>
                                <th class="py-3 px-4">Total Nilai</th>
                                <th class="py-3 px-4">Terbayar</th>
                                <th class="py-3 px-4">Sisa Tagihan</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200/80 dark:divide-zinc-800 text-xs">
                            @foreach($invoices as $inv)
                                @php $badge = $inv->status_badge; @endphp
                                <tr class="hover:bg-zinc-50/70 dark:hover:bg-zinc-800/40 transition-colors">
                                    <td class="py-3.5 px-4 font-mono font-bold text-zinc-900 dark:text-white">
                                        {{ $inv->invoice_number }}
                                        <div class="text-[10px] font-normal text-zinc-400 mt-0.5">
                                            {{ $inv->created_at->translatedFormat('d M Y') }}
                                        </div>
                                    </td>
                                    @if($isAdmin)
                                        <td class="py-3.5 px-4">
                                            <div class="font-bold text-zinc-900 dark:text-white">
                                                {{ $inv->client?->name ?? 'Klien' }}
                                            </div>
                                            <div class="text-[11px] text-zinc-500 font-mono">
                                                {{ $inv->client?->phone ?? $inv->client?->email }}
                                            </div>
                                        </td>
                                    @endif
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-zinc-800 dark:text-zinc-200">
                                            {{ $inv->project?->name ?? 'Proyek' }}
                                        </div>
                                        <div class="text-[11px] text-zinc-500 dark:text-zinc-400">
                                            {{ $inv->title }}
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 font-semibold text-zinc-900 dark:text-white">
                                        Rp {{ number_format($inv->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 font-semibold text-emerald-600 dark:text-emerald-400">
                                        Rp {{ number_format($inv->paid_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4 font-bold {{ $inv->balance_due > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-zinc-500' }}">
                                        Rp {{ number_format($inv->balance_due, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $badge['class'] }}">
                                            <span class="material-symbols-outlined text-[14px]">{{ $badge['icon'] }}</span>
                                            <span>{{ $badge['label'] }}</span>
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="inline-flex items-center gap-1.5 justify-end">
                                            @if($inv->paid_amount > 0)
                                                <a href="{{ route('invoices.receipt', $inv->id) }}" target="_blank" title="Buka Kwitansi Resmi" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-blue-200 dark:border-blue-800 bg-blue-50/50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-300 hover:bg-blue-100 font-bold text-xs transition-colors">
                                                    <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                                                    <span>Kwitansi</span>
                                                </a>
                                            @endif
                                            <a href="{{ route('invoices.show', $inv->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg {{ $isAdmin ? 'bg-emerald-600 hover:bg-emerald-500 text-white' : 'bg-zinc-900 dark:bg-zinc-100 hover:bg-zinc-800 text-white dark:text-zinc-900' }} font-bold text-xs transition-colors shadow-2xs">
                                                <span>{{ $isAdmin ? 'Kelola & Verifikasi' : 'Detail & Bayar' }}</span>
                                                <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($invoices->hasPages())
                    <div class="p-4 border-t border-zinc-200/80 dark:border-zinc-800">
                        {{ $invoices->links() }}
                    </div>
                @endif
            @endif
        </div>

    </div>
</x-app-layout>

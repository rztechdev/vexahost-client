<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('projects.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ $project->name }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $project->client->name }}</p>
                </div>
            </div>
            @can('update', $project)
            <a href="{{ route('projects.edit', $project) }}"
               class="inline-flex items-center gap-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 font-semibold py-2 px-4 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit Proyek
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-4">
        <div class="w-full space-y-6">

            @if(session('success'))
                <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg">{{ session('success') }}</div>
            @endif

            {{-- Project Info Cards --}}
            @php
                $total = $project->tasks->count();
                $done  = $project->tasks->where('status','done')->count();
                $inReview = $project->tasks->where('status','review')->count();
                $inProgress = $project->tasks->where('status','in_progress')->count();
                $pct   = $project->progress_percentage;

                $statusColors = ['pending'=>'yellow','active'=>'blue','completed'=>'green','archived'=>'gray'];
                $color = $statusColors[$project->work_status] ?? 'gray';

                $barColor = match(true) {
                    $pct >= 100 => 'bg-emerald-600',
                    $pct >= 80  => 'bg-amber-500',
                    $pct >= 40  => 'bg-sky-500',
                    default     => 'bg-zinc-400',
                };
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 md:col-span-2 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wide">Progress Keseluruhan</p>
                            <span class="text-[11px] font-bold px-2 py-0.5 rounded-full {{ $pct >= 100 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300' : ($pct >= 80 ? 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300' : 'bg-sky-100 text-sky-700 dark:bg-sky-950 dark:text-sky-300') }}">
                                @if($pct >= 100)
                                    Selesai (100%)
                                @elseif($inReview > 0)
                                    Tahap Review ({{ $pct }}%)
                                @elseif($inProgress > 0)
                                    Dikerjakan ({{ $pct }}%)
                                @else
                                    Antrean ({{ $pct }}%)
                                @endif
                            </span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex-1">
                                <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-3 overflow-hidden p-0.5">
                                    <div class="{{ $barColor }} h-2 rounded-full transition-all duration-700" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                            <span class="text-2xl font-black text-gray-800 dark:text-white font-mono tracking-tight">{{ $pct }}%</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mt-2 pt-1 border-t border-zinc-100 dark:border-zinc-700/50">
                        <span>{{ $done }} dari {{ $total }} tugas tuntas</span>
                        @if($inReview > 0)
                            <span class="text-amber-600 dark:text-amber-400 font-semibold flex items-center gap-1">
                                <span class="material-symbols-outlined text-[15px]">rate_review</span>
                                <span>Menunggu Peninjauan Klien</span>
                            </span>
                        @elseif($inProgress > 0)
                            <span class="text-sky-600 dark:text-sky-400 font-semibold flex items-center gap-1">
                                <span class="material-symbols-outlined text-[15px]">pending</span>
                                <span>Sedang Dikerjakan</span>
                            </span>
                        @elseif($pct >= 100)
                            <span class="text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-1">
                                <span class="material-symbols-outlined text-[15px]">task_alt</span>
                                <span>Proyek Tuntas</span>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1 font-medium uppercase tracking-wide">Status</p>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-sm font-semibold bg-{{ $color }}-100 text-{{ $color }}-800 dark:bg-{{ $color }}-900 dark:text-{{ $color }}-200">
                        {{ $project->work_status_label }}
                    </span>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1 font-medium uppercase tracking-wide">Deadline</p>
                    <p class="text-sm font-semibold text-gray-800 dark:text-white">
                        {{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('d M Y') : 'Tidak ditentukan' }}
                    </p>
                    @if($project->end_date && \Carbon\Carbon::parse($project->end_date)->isPast() && $project->work_status !== 'completed')
                        <p class="text-xs text-red-500 mt-0.5">Melewati deadline!</p>
                    @endif
                </div>
            </div>

            {{-- Website Klien (Jika ada) --}}
            @if($project->link_website)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-wrap items-center justify-between gap-3 border-l-4 border-emerald-500">
                <div class="flex items-center gap-2.5">
                    <span class="material-symbols-outlined text-emerald-600 text-2xl">language</span>
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white text-sm">Website Proyek Klien</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Tautan website resmi atau link pratinjau sistem</p>
                    </div>
                </div>
                <a href="{{ $project->link_website }}" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-mono text-xs font-bold transition shadow-xs">
                    <span>{{ $project->link_website }}</span>
                    <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                </a>
            </div>
            @endif

            {{-- Subscription / Masa Berlaku Info --}}
            @if($project->hasActiveSubscription())
            @php
                $subColor = $project->subscription_status_color;
                $sisaHari = $project->subscription_sisa_hari;
                $subStatusLabel = $project->subscription_status_label;
                $companySettingsSub = $companySettings ?? \App\Models\CompanySetting::get();
            @endphp

            {{-- Alert Banner for akan_expired / expired --}}
            @if(in_array($project->subscription_status, ['akan_expired', 'expired']))
            <div class="bg-gradient-to-r {{ $project->subscription_status === 'expired' ? 'from-red-500/15 via-red-500/10 to-rose-500/10 border-red-400 dark:border-red-600/80' : 'from-amber-500/15 via-amber-500/10 to-orange-500/10 border-amber-400 dark:border-amber-600/80' }} border-2 rounded-2xl p-5 sm:p-6 shadow-md space-y-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-start gap-3.5">
                        <div class="w-12 h-12 rounded-xl {{ $project->subscription_status === 'expired' ? 'bg-red-500' : 'bg-amber-500' }} text-white flex items-center justify-center shrink-0 shadow-sm">
                            <span class="material-symbols-outlined text-2xl">{{ $project->subscription_status === 'expired' ? 'error' : 'schedule' }}</span>
                        </div>
                        <div class="space-y-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-{{ $subColor }}-100 text-{{ $subColor }}-800 dark:bg-{{ $subColor }}-950 dark:text-{{ $subColor }}-300 border border-{{ $subColor }}-300 dark:border-{{ $subColor }}-700">
                                    {{ $subStatusLabel }}
                                </span>
                                <span class="text-xs text-{{ $subColor }}-800 dark:text-{{ $subColor }}-300 font-bold flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">lock_clock</span>
                                    <span>Masa Berlaku {{ $project->subscription_type_label }}</span>
                                </span>
                            </div>
                            <h3 class="text-base sm:text-lg font-black text-zinc-900 dark:text-white">
                                @if($project->subscription_status === 'expired')
                                    Masa Berlaku Layanan Telah Berakhir
                                @else
                                    Masa Berlaku Layanan Akan Segera Berakhir
                                @endif
                            </h3>
                            <p class="text-xs sm:text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed max-w-3xl">
                                @if($project->subscription_status === 'expired')
                                    Layanan proyek <strong>{{ $project->name }}</strong> (domain, hosting, lisensi aplikasi) telah melewati tanggal expired
                                    (<strong>{{ $project->subscription_expired->format('d M Y') }}</strong>).
                                    Segera lakukan perpanjangan agar layanan tetap aktif.
                                @else
                                    Layanan proyek <strong>{{ $project->name }}</strong> akan berakhir pada
                                    <strong>{{ $project->subscription_expired->format('d M Y') }}</strong>
                                    (<strong>{{ $sisaHari }} hari lagi</strong>).
                                    Silakan lakukan perpanjangan sebelum masa berlaku habis.
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-zinc-900 border border-{{ $subColor }}-300 dark:border-{{ $subColor }}-700/60 p-4 rounded-xl shrink-0 min-w-[220px] text-center md:text-right shadow-xs">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 block">Biaya Perpanjangan</span>
                        <span class="text-2xl font-black text-{{ $subColor }}-600 dark:text-{{ $subColor }}-400 font-mono tracking-tight block mt-0.5">
                            Rp {{ number_format($project->subscription_price, 0, ',', '.') }}
                        </span>
                        <span class="text-[10px] text-zinc-500 dark:text-zinc-400 block mt-1">
                            Periode: {{ $project->subscription_type_label }}
                            @if($project->auto_renew)
                                • <span class="text-emerald-600 dark:text-emerald-400 font-semibold">Auto-Renew Aktif</span>
                            @endif
                        </span>
                    </div>
                </div>

                <div class="pt-4 border-t {{ $project->subscription_status === 'expired' ? 'border-red-200/80 dark:border-red-700/60' : 'border-amber-200/80 dark:border-amber-700/60' }} flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2 text-xs text-zinc-600 dark:text-zinc-300">
                        <span class="material-symbols-outlined text-{{ $subColor }}-600 text-[18px]">account_balance</span>
                        <span>Transfer {{ $companySettingsSub->bank_name ?? '-' }}: <strong>{{ $companySettingsSub->bank_account_number ?? '-' }} a.n {{ $companySettingsSub->bank_account_holder ?? '-' }}</strong> atau Scan QRIS Resmi</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="https://wa.me/6285155222886?text={{ urlencode('Halo VexaHost, saya ingin perpanjangan layanan proyek: ' . $project->name . ' (expired: ' . $project->subscription_expired->format('d M Y') . ')') }}" target="_blank"
                           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-{{ $subColor }}-500 hover:bg-{{ $subColor }}-600 text-white text-xs font-bold transition shadow-xs active:scale-95">
                            <span class="material-symbols-outlined text-[16px]">chat</span>
                            <span>Hubungi untuk Perpanjangan</span>
                        </a>
                    </div>
                </div>
            </div>
            @else
            {{-- Compact Subscription Info Card for active/diperpanjang --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 flex flex-wrap items-center justify-between gap-3 border-l-4 border-{{ $subColor }}-500">
                <div class="flex items-center gap-2.5">
                    <span class="material-symbols-outlined text-{{ $subColor }}-600 text-2xl">verified</span>
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white text-sm flex items-center gap-2">
                            Masa Berlaku Layanan
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-{{ $subColor }}-100 text-{{ $subColor }}-800 dark:bg-{{ $subColor }}-900 dark:text-{{ $subColor }}-200">
                                {{ $subStatusLabel }}
                            </span>
                        </h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $project->subscription_type_label }} •
                            Berlaku hingga <strong>{{ $project->subscription_expired?->format('d M Y') ?? '-' }}</strong>
                            @if($sisaHari !== null)
                                ({{ $sisaHari }} hari lagi)
                            @endif
                            @if($project->auto_renew)
                                • <span class="text-emerald-600 dark:text-emerald-400 font-semibold">Auto-Renew</span>
                            @endif
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 block">Biaya Perpanjangan</span>
                    <span class="text-lg font-black text-zinc-800 dark:text-white font-mono">Rp {{ number_format($project->subscription_price, 0, ',', '.') }}</span>
                </div>
            </div>
            @endif
            @endif

            {{-- Description --}}
            @if($project->description)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
                <h3 class="font-semibold text-gray-700 dark:text-gray-300 text-sm mb-2">Deskripsi Proyek</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">{{ $project->description }}</p>
            </div>
            @endif

            @php
                $canManageKanban = auth()->user()->hasRole(['admin', 'technician']) && !auth()->user()->hasRole('client');
                $inReview = $project->tasks->where('status', 'review')->count();
                $latestInvoice = $project->latestInvoice;
                $remainingBalance = $latestInvoice ? (float)$latestInvoice->balance_due : 0;
                $isSettlementNeeded = ($inReview > 0 || $project->status === 'review') && $remainingBalance > 0 && ($latestInvoice?->status !== 'paid');
                $companySettings = \App\Models\CompanySetting::get();
            @endphp

            {{-- Special Review & Settlement Notice for Client (STRICTLY ONLY WHEN IN REVIEW STAGE & HAS BALANCE DUE) --}}
            @if($isSettlementNeeded)
            <div class="bg-gradient-to-r from-amber-500/15 via-amber-500/10 to-orange-500/10 border-2 border-amber-400 dark:border-amber-600/80 rounded-2xl p-5 sm:p-6 shadow-md space-y-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex items-start gap-3.5">
                        <div class="w-12 h-12 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-sm">
                            <span class="material-symbols-outlined text-2xl">priority_high</span>
                        </div>
                        <div class="space-y-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 border border-amber-300 dark:border-amber-700">
                                    Tahap Peninjauan (Review Klien)
                                </span>
                                <span class="text-xs text-amber-800 dark:text-amber-300 font-bold flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">lock_clock</span>
                                    <span>Wajib Pelunasan Sebelum Go-Live &amp; Serah Terima</span>
                                </span>
                            </div>
                            <h3 class="text-base sm:text-lg font-black text-zinc-900 dark:text-white">
                                Pengerjaan Selesai Ditinjau? Lakukan Pelunasan Sebelum Peluncuran (Live)
                            </h3>
                            <p class="text-xs sm:text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed max-w-3xl">
                                Website/aplikasi Anda saat ini telah selesai dikerjakan dan berada pada tahap peninjauan (Review). Sesuai ketentuan layanan kami, 
                                <strong>sebelum proyek dipindahkan ke status Selesai (Done / Go-Live)</strong> dan serah terima akun hak akses penuh domain utama, 
                                mohon selesaikan <strong>pembayaran pelunasan sisa tagihan</strong> proyek Anda.
                            </p>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-zinc-900 border border-amber-300 dark:border-amber-700/60 p-4 rounded-xl shrink-0 min-w-[220px] text-center md:text-right shadow-xs">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 block">Sisa Tagihan Pelunasan</span>
                        <span class="text-2xl font-black text-amber-600 dark:text-amber-400 font-mono tracking-tight block mt-0.5">
                            Rp {{ number_format($remainingBalance, 0, ',', '.') }}
                        </span>
                        @if($latestInvoice)
                        <span class="text-[10px] text-zinc-500 dark:text-zinc-400 block mt-1">
                            Total: Rp {{ number_format($latestInvoice->amount, 0, ',', '.') }} • DP: Rp {{ number_format($latestInvoice->paid_amount, 0, ',', '.') }}
                        </span>
                        @endif
                    </div>
                </div>

                <!-- Actions & Payment Methods -->
                <div class="pt-4 border-t border-amber-200/80 dark:border-amber-700/60 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2 text-xs text-zinc-600 dark:text-zinc-300">
                        <span class="material-symbols-outlined text-amber-600 text-[18px]">account_balance</span>
                        <span>Transfer {{ $companySettings->bank_name ?? '-' }}: <strong>{{ $companySettings->bank_account_number ?? '-' }} a.n {{ $companySettings->bank_account_holder ?? '-' }}</strong> atau Scan QRIS Resmi</span>
                    </div>

                    <div class="flex items-center gap-2">
                        @if($latestInvoice)
                        <a href="{{ route('invoices.show', $latestInvoice) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold transition shadow-xs active:scale-95">
                            <span class="material-symbols-outlined text-[16px]">payments</span>
                            <span>Lihat Invoice &amp; Bayar Pelunasan</span>
                        </a>
                        <a href="{{ route('invoices.settlement-pdf', $latestInvoice) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white dark:bg-zinc-800 border border-amber-300 dark:border-amber-700 text-amber-800 dark:text-amber-300 text-xs font-bold hover:bg-amber-50 dark:hover:bg-zinc-700 transition shadow-xs" title="Unduh dokumen resmi Invoice Tagihan Pelunasan (PDF)">
                            <span class="material-symbols-outlined text-[16px] text-amber-600">description</span>
                            <span>Dokumen Pelunasan (PDF)</span>
                        </a>
                        <a href="{{ route('invoices.download-pdf', $latestInvoice) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-200 text-xs font-bold hover:bg-zinc-50 dark:hover:bg-zinc-700 transition shadow-xs" title="Unduh invoice kontrak proyek">
                            <span class="material-symbols-outlined text-[16px]">download</span>
                            <span>Invoice Proyek (PDF)</span>
                        </a>
                        @else
                        <a href="{{ route('invoices.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold transition shadow-xs">
                            <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                            <span>Buka Halaman Invoice</span>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- Tasks Kanban Board Section --}}
            <div x-data="{
                canManage: {{ $canManageKanban ? 'true' : 'false' }},
                remainingBalance: {{ $remainingBalance }},
                remainingBalanceFormatted: '{{ number_format($remainingBalance, 0, ',', '.') }}',
                draggedTaskId: null,
                dragOverColumn: null,
                isUpdating: false,
                openReviewModal: false,
                pendingTaskId: null,
                websiteUrl: '{{ $project->link_website ?? '' }}',
                sendWa: true,

                handleMove(taskId, newStatus) {
                    if (!this.canManage) return;
                    if (newStatus === 'review') {
                        this.pendingTaskId = taskId;
                        this.openReviewModal = true;
                        return;
                    }
                    if (newStatus === 'done' && this.remainingBalance > 0) {
                        VhSwal.confirm(
                            `Perhatian: Proyek ini masih memiliki sisa tagihan pelunasan sebesar Rp ${this.remainingBalanceFormatted} yang belum lunas.\n\nApakah Anda yakin ingin memindahkan tugas ke Selesai sebelum pelunasan diterima?`,
                            () => this.executeMove(taskId, newStatus, this.websiteUrl, this.sendWa)
                        );
                        return;
                    }
                    this.executeMove(taskId, newStatus, this.websiteUrl, this.sendWa);
                },

                async executeMove(taskId, newStatus, websiteUrl = null, sendWa = true) {
                    if (!this.canManage || this.isUpdating || !taskId) return;
                    this.isUpdating = true;
                    try {
                        const res = await fetch(`/tasks/${taskId}/progress`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ 
                                status: newStatus,
                                link_website: websiteUrl,
                                send_wa: sendWa
                            })
                        });
                        const data = await res.json();
                        if (data.success) {
                            window.location.reload();
                        } else {
                            VhSwal.error(data.message || 'Gagal mengubah status tugas');
                            this.isUpdating = false;
                        }
                    } catch(err) {
                        console.error(err);
                        VhSwal.error('Terjadi kesalahan koneksi saat memindahkan tugas.');
                        this.isUpdating = false;
                    }
                }
            }" class="space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-bold text-zinc-900 dark:text-white text-lg flex items-center gap-2">
                                <span class="material-symbols-outlined text-emerald-600">view_kanban</span>
                                <span>Papan Kanban Tugas Proyek</span>
                            </h3>
                            @if(!$canManageKanban)
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700 inline-flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[13px]">lock</span>
                                    <span>Mode Baca</span>
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                            @if($canManageKanban)
                                Geser (drag & drop) tugas antar kolom atau gunakan tombol cepat untuk memperbarui progres &amp; sinkronisasi ke CRM.
                            @else
                                Pantau tahapan dan progres tugas pengerjaan proyek secara real-time.
                            @endif
                        </p>
                    </div>
                    @if(auth()->user()->hasRole('admin'))
                    <a href="{{ route('tasks.create', ['project_id' => $project->id]) }}"
                       class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-2.5 px-4 rounded-xl shadow-xs transition-all active:scale-95">
                        <span class="material-symbols-outlined text-[18px]">add</span>
                        <span>Tambah Tugas</span>
                    </a>
                    @endif
                </div>

                @php
                    $columns = [
                        'todo' => [
                            'label' => 'To Do',
                            'badge' => 'Menunggu',
                            'icon' => 'format_list_bulleted',
                            'theme' => 'zinc',
                            'border' => 'border-zinc-300 dark:border-zinc-700',
                            'header_bg' => 'bg-zinc-100 dark:bg-zinc-800/80 text-zinc-800 dark:text-zinc-200',
                            'pill' => 'bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300'
                        ],
                        'in_progress' => [
                            'label' => 'In Progress',
                            'badge' => 'Dikerjakan',
                            'icon' => 'pending',
                            'theme' => 'sky',
                            'border' => 'border-sky-300 dark:border-sky-800',
                            'header_bg' => 'bg-sky-50 dark:bg-sky-950/40 text-sky-800 dark:text-sky-300',
                            'pill' => 'bg-sky-200/80 dark:bg-sky-800 text-sky-800 dark:text-sky-200'
                        ],
                        'review' => [
                            'label' => 'Review',
                            'badge' => 'Peninjauan',
                            'icon' => 'rate_review',
                            'theme' => 'amber',
                            'border' => 'border-amber-300 dark:border-amber-800',
                            'header_bg' => 'bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300',
                            'pill' => 'bg-amber-200/80 dark:bg-amber-800 text-amber-800 dark:text-amber-200'
                        ],
                        'done' => [
                            'label' => 'Done',
                            'badge' => 'Selesai',
                            'icon' => 'task_alt',
                            'theme' => 'emerald',
                            'border' => 'border-emerald-300 dark:border-emerald-800',
                            'header_bg' => 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300',
                            'pill' => 'bg-emerald-200/80 dark:bg-emerald-800 text-emerald-800 dark:text-emerald-200'
                        ],
                    ];
                @endphp

                <!-- 4 Columns Kanban Board Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-start">
                    @foreach($columns as $status => $col)
                    @php $columnTasks = $project->tasks->where('status', $status); @endphp
                    <div class="rounded-2xl bg-zinc-100/70 dark:bg-zinc-900/60 border border-zinc-200/80 dark:border-zinc-800 p-3 min-h-[420px] flex flex-col transition-all duration-200"
                         @if($canManageKanban)
                         :class="dragOverColumn === '{{ $status }}' ? 'ring-2 ring-emerald-500 bg-emerald-50/40 dark:bg-emerald-950/30 border-emerald-400 dark:border-emerald-700' : ''"
                         @dragover.prevent="dragOverColumn = '{{ $status }}'"
                         @dragleave="if (dragOverColumn === '{{ $status }}') dragOverColumn = null"
                         @drop.prevent="if (draggedTaskId) { const tid = draggedTaskId; draggedTaskId = null; dragOverColumn = null; handleMove(tid, '{{ $status }}'); }"
                         @endif>
                        
                        <!-- Column Header -->
                        <div class="flex items-center justify-between p-2.5 mb-3 rounded-xl {{ $col['header_bg'] }} border {{ $col['border'] }}">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px]">{{ $col['icon'] }}</span>
                                <div>
                                    <span class="font-bold text-xs">{{ $col['label'] }}</span>
                                    <span class="block text-[10px] opacity-75 font-normal">{{ $col['badge'] }}</span>
                                </div>
                            </div>
                            <span class="text-xs font-mono font-bold rounded-full px-2.5 py-0.5 {{ $col['pill'] }}">
                                {{ $columnTasks->count() }}
                            </span>
                        </div>

                        <!-- Task Cards Dropzone -->
                        <div class="space-y-3 flex-1 flex flex-col">
                            @forelse($columnTasks as $task)
                            @php
                                $priorityConfig = [
                                    'high'   => ['label' => 'Tinggi', 'bg' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400 border-rose-200/60 dark:border-rose-800/40'],
                                    'medium' => ['label' => 'Sedang', 'bg' => 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400 border-amber-200/60 dark:border-amber-800/40'],
                                    'low'    => ['label' => 'Rendah', 'bg' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border-emerald-200/60 dark:border-emerald-800/40'],
                                ];
                                $pc = $priorityConfig[$task->priority] ?? ['label' => 'Normal', 'bg' => 'bg-zinc-100 text-zinc-700 border-zinc-200'];
                            @endphp
                            <div @if($canManageKanban)
                                     draggable="true"
                                     @dragstart="draggedTaskId = {{ $task->id }}"
                                     @dragend="draggedTaskId = null; dragOverColumn = null"
                                     class="bg-white dark:bg-zinc-800 rounded-xl p-3.5 shadow-xs hover:shadow-md border border-zinc-200/80 dark:border-zinc-700/80 transition-all duration-200 cursor-grab active:cursor-grabbing group space-y-2.5"
                                 @else
                                     draggable="false"
                                     class="bg-white dark:bg-zinc-800 rounded-xl p-3.5 shadow-xs border border-zinc-200/80 dark:border-zinc-700/80 transition-all duration-200 cursor-default group space-y-2.5"
                                 @endif>
                                
                                <!-- Card Header: Priority & Drag Handle -->
                                <div class="flex items-center justify-between">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide border {{ $pc['bg'] }}">
                                        {{ $pc['label'] }}
                                    </span>
                                    @if($canManageKanban)
                                    <span class="material-symbols-outlined text-[16px] text-zinc-400 group-hover:text-zinc-600 dark:group-hover:text-zinc-300 transition-colors" title="Geser tugas">
                                        drag_indicator
                                    </span>
                                    @endif
                                </div>

                                <!-- Title -->
                                <div>
                                    <a href="{{ route('tasks.show', $task) }}" class="text-xs font-bold text-zinc-900 dark:text-white hover:text-emerald-600 dark:hover:text-emerald-400 leading-snug line-clamp-2 block transition-colors">
                                        {{ $task->name }}
                                    </a>
                                    @if($task->description)
                                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 line-clamp-2 mt-1 leading-relaxed">{{ $task->description }}</p>
                                    @endif
                                </div>

                                <!-- Assignee & Deadline -->
                                <div class="space-y-1 pt-1.5 border-t border-zinc-100 dark:border-zinc-700/60 text-[11px]">
                                    @if($task->assignee)
                                    <div class="flex items-center gap-1.5 text-zinc-600 dark:text-zinc-400">
                                        <span class="material-symbols-outlined text-[14px] text-zinc-400">person</span>
                                        <span class="truncate">{{ $task->assignee->name }}</span>
                                    </div>
                                    @endif

                                    @if($task->due_date)
                                    @php $isPast = \Carbon\Carbon::parse($task->due_date)->isPast() && $task->status !== 'done'; @endphp
                                    <div class="flex items-center gap-1.5 {{ $isPast ? 'text-rose-600 dark:text-rose-400 font-bold' : 'text-zinc-500 dark:text-zinc-400' }}">
                                        <span class="material-symbols-outlined text-[14px] {{ $isPast ? 'text-rose-500' : 'text-zinc-400' }}">event</span>
                                        <span>{{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}</span>
                                        @if($isPast)
                                            <span class="text-[9px] uppercase px-1 rounded bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300">Lewat</span>
                                        @endif
                                    </div>
                                    @endif
                                </div>

                                <!-- Stage Status & Actions -->
                                <div class="pt-2 border-t border-zinc-100 dark:border-zinc-700/60 flex items-center justify-between gap-1">
                                    @if($canManageKanban)
                                    <div class="flex items-center gap-1">
                                        @if($task->status === 'todo')
                                            <button type="button" @click="handleMove({{ $task->id }}, 'in_progress')" 
                                                    class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-sky-50 dark:bg-sky-950/50 hover:bg-sky-100 text-sky-700 dark:text-sky-300 text-[10px] font-bold border border-sky-200 dark:border-sky-800 transition" title="Mulai kerjakan">
                                                <span>Kerjakan</span>
                                                <span class="material-symbols-outlined text-[12px]">arrow_forward</span>
                                            </button>
                                        @elseif($task->status === 'in_progress')
                                            <button type="button" @click="handleMove({{ $task->id }}, 'todo')" 
                                                    class="inline-flex items-center gap-0.5 px-1.5 py-1 rounded-md bg-zinc-100 dark:bg-zinc-700 hover:bg-zinc-200 text-zinc-600 dark:text-zinc-300 text-[10px] font-bold transition" title="Kembalikan ke To Do">
                                                <span class="material-symbols-outlined text-[12px]">arrow_back</span>
                                            </button>
                                            <button type="button" @click="handleMove({{ $task->id }}, 'review')" 
                                                    class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-amber-50 dark:bg-amber-950/50 hover:bg-amber-100 text-amber-700 dark:text-amber-300 text-[10px] font-bold border border-amber-200 dark:border-amber-800 transition" title="Kirim ke Review">
                                                <span>Review</span>
                                                <span class="material-symbols-outlined text-[12px]">arrow_forward</span>
                                            </button>
                                        @elseif($task->status === 'review')
                                            <button type="button" @click="handleMove({{ $task->id }}, 'in_progress')" 
                                                    class="inline-flex items-center gap-0.5 px-1.5 py-1 rounded-md bg-zinc-100 dark:bg-zinc-700 hover:bg-zinc-200 text-zinc-600 dark:text-zinc-300 text-[10px] font-bold transition" title="Revisi kembali ke In Progress">
                                                <span class="material-symbols-outlined text-[12px]">arrow_back</span>
                                            </button>
                                            <button type="button" @click="handleMove({{ $task->id }}, 'done')" 
                                                    class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-emerald-50 dark:bg-emerald-950/50 hover:bg-emerald-100 text-emerald-700 dark:text-emerald-300 text-[10px] font-bold border border-emerald-200 dark:border-emerald-800 transition" title="Tandai Selesai">
                                                <span class="material-symbols-outlined text-[12px]">check</span>
                                                <span>Selesai</span>
                                            </button>
                                        @elseif($task->status === 'done')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold text-emerald-600 dark:text-emerald-400">
                                                <span class="material-symbols-outlined text-[14px]">done_all</span>
                                                <span>Selesai</span>
                                            </span>
                                            <button type="button" @click="handleMove({{ $task->id }}, 'review')" 
                                                    class="text-[10px] text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 hover:underline">
                                                Buka Lagi
                                            </button>
                                        @endif
                                    </div>
                                    @else
                                    <div class="flex items-center gap-1">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold {{ $col['pill'] }}">
                                            <span class="material-symbols-outlined text-[12px]">{{ $col['icon'] }}</span>
                                            <span>{{ $col['badge'] }}</span>
                                        </span>
                                    </div>
                                    @endif

                                    <div class="flex items-center gap-1">
                                        <a href="{{ route('tasks.show', $task) }}" class="text-[10px] font-bold text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200">
                                            Detail
                                        </a>
                                        @if(auth()->user()->hasRole('admin'))
                                            <span class="text-zinc-300 dark:text-zinc-600">•</span>
                                            <a href="{{ route('tasks.edit', $task) }}" class="text-[10px] font-bold text-emerald-600 hover:text-emerald-700">
                                                Edit
                                            </a>
                                            <span class="text-zinc-300 dark:text-zinc-600">•</span>
                                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline" x-ref="deleteTask{{ $task->id }}">
                                                @csrf @method('DELETE')
                                                <button type="button" @click="VhSwal.confirmDelete('Hapus tugas ini?', $refs.deleteTask{{ $task->id }})" class="text-[10px] font-bold text-rose-500 hover:text-rose-700">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="flex-1 flex flex-col items-center justify-center border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-xl p-4 text-center text-zinc-400 dark:text-zinc-600">
                                <span class="material-symbols-outlined text-2xl mb-1 text-zinc-300 dark:text-zinc-700">move_to_inbox</span>
                                <p class="text-[11px] font-medium">Belum ada tugas di kolom ini</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                    @endforeach
                </div>

                @if($canManageKanban)
                <!-- Modal Input Link Website saat Pindah ke Review -->
                <div x-show="openReviewModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-950/60 backdrop-blur-xs"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4"
                         @click.outside="openReviewModal = false">
                        <div class="flex items-center justify-between pb-3 border-b border-zinc-100 dark:border-zinc-800">
                            <h3 class="text-base font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                                <span class="material-symbols-outlined text-amber-500">rate_review</span>
                                <span>Tahap Review &amp; Pratinjau Website</span>
                            </h3>
                            <button type="button" @click="openReviewModal = false" class="text-zinc-400 hover:text-zinc-600">
                                <span class="material-symbols-outlined text-[20px]">close</span>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs text-zinc-600 dark:text-zinc-400 space-y-1">
                                <div>Proyek: <strong class="text-zinc-900 dark:text-white">{{ $project->name }}</strong></div>
                                <div>Status Baru: <span class="font-bold text-amber-600 dark:text-amber-400">Review Klien (Peninjauan)</span></div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase text-zinc-700 dark:text-zinc-300 mb-1">
                                    Link URL Website Pratinjau / Preview <span class="text-rose-500">*</span>
                                </label>
                                <input type="url" x-model="websiteUrl" required placeholder="https://domainklien.com"
                                       class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-xs font-mono font-bold text-zinc-900 dark:text-white focus:ring-emerald-500">
                                <p class="text-[11px] text-zinc-400 mt-1">Masukkan URL website aktif atau tautan demo yang siap ditinjau oleh klien.</p>
                            </div>

                            <div class="p-3.5 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-emerald-600">chat</span>
                                    <div>
                                        <p class="text-xs font-bold text-zinc-900 dark:text-white">Kirim WhatsApp Otomatis ke Klien</p>
                                        <p class="text-[10px] text-zinc-400">Perbarui status proyek &amp; kirim link review via WA resmi</p>
                                    </div>
                                </div>
                                <input type="checkbox" x-model="sendWa" class="rounded text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                            </div>

                            <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800 flex justify-end gap-2">
                                <button type="button" @click="openReviewModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold border text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition">
                                    Batal
                                </button>
                                <button type="button" 
                                        @click="
                                            if (!websiteUrl) { VhSwal.warning('Silakan masukkan link URL website pratinjau terlebih dahulu.'); return; }
                                            openReviewModal = false;
                                            executeMove(pendingTaskId, 'review', websiteUrl, sendWa);
                                        " 
                                        class="px-5 py-2 rounded-xl text-xs font-bold bg-amber-500 hover:bg-amber-600 text-white shadow-sm inline-flex items-center gap-1.5 transition active:scale-95">
                                    <span class="material-symbols-outlined text-[16px]">check</span>
                                    <span>Simpan &amp; Kirim ke Review</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Documents Section --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6">
                <h3 class="font-bold text-gray-800 dark:text-white text-lg mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-500">folder</span>
                    <span>Dokumen Proyek</span>
                </h3>

                {{-- Upload Form --}}
                <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="mb-6 p-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl hover:border-emerald-400 transition-colors">
                    @csrf
                    <input type="hidden" name="documentable_type" value="project">
                    <input type="hidden" name="documentable_id" value="{{ $project->id }}">
                    <div class="flex items-center gap-4">
                        <label class="flex-1 flex items-center gap-3 cursor-pointer">
                            <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Upload Dokumen</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">PDF, DOC, XLS, PNG, JPG — Maks. 20MB</p>
                            </div>
                            <input type="file" name="file" id="file" class="hidden" required onchange="document.getElementById('file-name').textContent = this.files[0].name">
                        </label>
                        <p id="file-name" class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs"></p>
                        <button type="submit" class="shrink-0 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold py-2 px-5 rounded-lg transition">Upload</button>
                    </div>
                </form>

                {{-- Document List --}}
                @if($project->documents->isEmpty())
                    <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-4">Belum ada dokumen diupload.</p>
                @else
                <div class="space-y-2">
                    @foreach($project->documents as $doc)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900/40 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-900/40 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $doc->file_name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Diupload oleh {{ $doc->uploader->name }} • {{ $doc->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('documents.download', $doc) }}"
                               class="text-sm text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 font-medium flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Download
                            </a>
                            @if(auth()->user()->hasRole('admin') || $doc->uploaded_by === auth()->id())
                            <form action="{{ route('documents.destroy', $doc) }}" method="POST" x-data x-ref="deleteDoc{{ $doc->id }}">
                                @csrf @method('DELETE')
                                <button type="button" @click="VhSwal.confirmDelete('Hapus dokumen ini?', $refs.deleteDoc{{ $doc->id }})" class="text-red-500 hover:text-red-700 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>


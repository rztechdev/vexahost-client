<x-app-layout>
    <div class="w-full space-y-6">
        
        <!-- Navigation Back & Title -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('invoices.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 mb-2 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                    <span>Kembali ke Daftar Tagihan</span>
                </a>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-black text-zinc-900 dark:text-white tracking-tight">
                        {{ $invoice->invoice_number }}
                    </h1>
                    @php $badge = $invoice->status_badge; @endphp
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $badge['class'] }}">
                        <span class="material-symbols-outlined text-[16px]">{{ $badge['icon'] }}</span>
                        <span>{{ $badge['label'] }}</span>
                    </span>
                </div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                    {{ $invoice->title }} &bull; Proyek: <strong>{{ $invoice->project?->name ?? '-' }}</strong>
                </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('invoices.download-pdf', $invoice) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 hover:border-emerald-500 text-zinc-700 dark:text-zinc-300 text-xs font-bold transition-all shadow-2xs hover:shadow-xs">
                    <span class="material-symbols-outlined text-[18px] text-rose-500">picture_as_pdf</span>
                    <span>Unduh PDF Tagihan</span>
                </a>

                @if($invoice->status === 'paid' || $invoice->status === 'partially_paid' || $invoice->paid_amount > 0)
                    <a href="{{ route('invoices.receipt', $invoice) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800 hover:border-blue-500 text-blue-700 dark:text-blue-300 text-xs font-bold transition-all shadow-2xs hover:shadow-xs">
                        <span class="material-symbols-outlined text-[18px] text-blue-600">receipt_long</span>
                        <span>{{ $invoice->status === 'partially_paid' ? 'Lihat Kwitansi DP' : 'Lihat Kwitansi Lunas' }}</span>
                    </a>
                @endif

                @if($isAdmin)
                    @php
                        $clientCleanPhone = !empty($invoice->client?->phone) ? preg_replace('/[^0-9]/', '', $invoice->client->phone) : '';
                        $waMsgToClient = rawurlencode("Halo Kak " . ($invoice->client?->name ?? 'Klien') . ",\n\nKami dari Tim Finance PT DESTINARA CHAKRAWALA ARTHA ingin mengonfirmasi perihal tagihan proyek *" . ($invoice->project?->name ?? 'Proyek') . "* (No. Invoice: {$invoice->invoice_number}).\n\nStatus saat ini: *" . $badge['label'] . "*\nSisa Tagihan: *Rp " . number_format($invoice->balance_due, 0, ',', '.') . "*.\n\nApakah ada yang bisa kami bantu? Terima kasih!");
                    @endphp
                    @if($clientCleanPhone)
                        <a href="https://wa.me/{{ $clientCleanPhone }}?text={{ $waMsgToClient }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all shadow-xs">
                            <span class="material-symbols-outlined text-[18px]">chat</span>
                            <span>Chat WA Klien</span>
                        </a>
                    @endif
                @else
                    @php
                        $waPhone = !empty($settings->phone_support) ? preg_replace('/[^0-9]/', '', $settings->phone_support) : '6285808749131';
                        $waShareText = rawurlencode("Halo Tim Finance {$settings->company_name},\n\nSaya ingin mengonfirmasi pembayaran untuk tagihan:\n- No. Invoice: {$invoice->invoice_number}\n- Proyek: {$invoice->project?->name}\n- Total Nilai: Rp " . number_format($invoice->amount, 0, ',', '.') . "\n- Sisa Tagihan: Rp " . number_format($invoice->balance_due, 0, ',', '.') . "\n\nTerlampir bukti transfer saya. Mohon dicek. Terima kasih!");
                    @endphp
                    <a href="https://wa.me/{{ $waPhone }}?text={{ $waShareText }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all shadow-xs">
                        <span class="material-symbols-outlined text-[18px]">chat</span>
                        <span>Konfirmasi via WhatsApp</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Mode Administrator Banner (If Admin) -->
        @if($isAdmin)
            <div class="p-3.5 rounded-xl bg-zinc-900 border border-zinc-800 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-xs">
                <div class="flex items-center gap-2.5">
                    <span class="material-symbols-outlined text-emerald-400 text-[20px]">shield_person</span>
                    <div class="text-xs">
                        <span class="font-bold text-white uppercase tracking-wider text-[11px] bg-emerald-950 text-emerald-300 border border-emerald-800/60 px-2 py-0.5 rounded mr-1">Admin Mode</span>
                        <span class="text-zinc-300">Anda sedang meninjau tagihan klien <strong>{{ $invoice->client?->name ?? 'Klien' }}</strong> &bull; Proyek: <strong>{{ $invoice->project?->name ?? '-' }}</strong></span>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="https://client.vexahostcloud.my.id/admin/projects" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-zinc-800 hover:bg-zinc-700 text-zinc-200 text-xs font-bold transition-all border border-zinc-700">
                        <span class="material-symbols-outlined text-[15px]">open_in_new</span>
                        <span>Buka CRM Proyek</span>
                    </a>
                </div>
            </div>
        @endif

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

        <!-- Main Invoice Grid Layout (2 Columns) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            
            <!-- Left Column: Official Invoice Details (2 cols) -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Main Invoice Printable Sheet -->
                <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs p-6 sm:p-8 space-y-6">
                    
                    <!-- Top Invoice Meta -->
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between pb-6 border-b border-zinc-200/60 dark:border-zinc-800 gap-4">
                        <div class="space-y-1">
                            <span class="text-[10px] font-mono uppercase tracking-wider text-zinc-400 font-bold">Penerbit Dokumen</span>
                            <div class="flex items-center gap-2">
                                <span class="font-black text-zinc-900 dark:text-white text-base tracking-tight">
                                    {{ $settings->company_name ?? 'PT DESTINARA CHAKRAWALA ARTHA' }}
                                </span>
                            </div>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed max-w-sm">
                                Layanan Pembuatan Website, Sistem POS &amp; Aplikasi Digital<br>
                                WhatsApp: {{ $settings->phone_support ?? '0858-0874-9131' }} &bull; Email: {{ $settings->email_support ?? 'vexahostcloudtech@gmail.com' }}
                            </p>
                        </div>
                        <div class="sm:text-right space-y-1">
                            <span class="text-[10px] font-mono uppercase tracking-wider text-zinc-400 font-bold">Tanggal Terbit</span>
                            <div class="text-xs font-bold text-zinc-900 dark:text-white">
                                {{ $invoice->created_at->translatedFormat('d F Y') }}
                            </div>
                            <div class="text-[11px] text-zinc-400 font-mono">
                                Status: <span class="font-bold text-zinc-700 dark:text-zinc-300">{{ $badge['label'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Client & Project Details -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 py-2">
                        <div>
                            <span class="text-[10px] font-mono uppercase tracking-wider text-zinc-400 font-bold block mb-1">Ditagihkan Kepada:</span>
                            <div class="text-sm font-bold text-zinc-900 dark:text-white">
                                {{ $invoice->client?->name ?? 'Klien Terhormat' }}
                            </div>
                            <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5 space-y-0.5">
                                <div>{{ $invoice->client?->email ?? '-' }}</div>
                                @if($invoice->client?->phone)
                                    <div>{{ $invoice->client->phone }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="sm:text-right">
                            <span class="text-[10px] font-mono uppercase tracking-wider text-zinc-400 font-bold block mb-1">Batas Waktu (Due Date):</span>
                            <div class="text-sm font-bold text-zinc-900 dark:text-white">
                                {{ $invoice->due_date ? $invoice->due_date->translatedFormat('d F Y') : now()->addDays(7)->translatedFormat('d F Y') }}
                            </div>
                            <span class="text-[11px] text-zinc-500 block mt-0.5">
                                Domisili: {{ $settings->domicile_city ?? 'Jakarta' }}, Banten
                            </span>
                        </div>
                    </div>

                    <!-- Financial Summary Table -->
                    <div class="pt-4">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="text-[11px] font-mono text-zinc-400 uppercase tracking-wider border-b border-zinc-200/60 dark:border-zinc-800">
                                    <th class="py-2.5">Deskripsi Layanan / Proyek</th>
                                    <th class="py-2.5 text-right">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                                <tr>
                                    <td class="py-3.5">
                                        <div class="font-bold text-zinc-900 dark:text-white">{{ $invoice->project?->name ?? 'Proyek' }}</div>
                                        <div class="text-[11px] text-zinc-500 mt-0.5">{{ $invoice->title }}</div>
                                    </td>
                                    <td class="py-3.5 text-right font-semibold text-zinc-900 dark:text-white">
                                        Rp {{ number_format($invoice->amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2.5 text-zinc-500">Telah Dibayar (Uang Muka / DP)</td>
                                    <td class="py-2.5 text-right font-semibold text-emerald-600 dark:text-emerald-400">
                                        - Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr class="border-t-2 border-zinc-900 dark:border-zinc-100 text-sm font-black">
                                    <td class="py-3 text-zinc-900 dark:text-white uppercase">Sisa Tagihan (Balance Due):</td>
                                    <td class="py-3 text-right {{ $invoice->balance_due > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                        Rp {{ number_format($invoice->balance_due, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>

                <!-- Section Below Table: Differentiated for Admin vs Client -->
                @if($isAdmin)
                    <!-- Admin View: Rekonsiliasi Kas Masuk & Akun Bank Perusahaan -->
                    <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs p-5 sm:p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3">
                            <h3 class="text-sm font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                                <span class="material-symbols-outlined text-emerald-600">account_balance</span>
                                <span>Rekonsiliasi Kas Masuk &amp; Akun Bank Perusahaan</span>
                            </h3>
                            <span class="text-[10px] font-mono uppercase px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 font-bold text-zinc-600 dark:text-zinc-400">
                                Info Finance
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                            <div class="p-3.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-800/40 space-y-1.5">
                                <div class="text-[10px] font-mono uppercase text-zinc-400 font-bold">Rekening Penerima Kas Masuk</div>
                                <div class="font-bold text-zinc-900 dark:text-white text-sm">{{ $settings->bank_name ?? '-' }}</div>
                                <div class="font-mono font-bold text-zinc-800 dark:text-zinc-200">{{ $settings->bank_account_number ?? '-' }}</div>
                                <div class="text-[11px] text-zinc-500">a.n {{ $settings->bank_account_holder ?? '-' }}</div>
                            </div>

                            <div class="p-3.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-800/40 space-y-1.5">
                                <div class="text-[10px] font-mono uppercase text-zinc-400 font-bold">Status Audit &amp; Verifikator</div>
                                <div class="font-bold text-zinc-900 dark:text-white text-sm flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[16px] {{ $invoice->status === 'paid' ? 'text-emerald-500' : ($invoice->status === 'partially_paid' ? 'text-blue-500' : ($invoice->status === 'verifying' ? 'text-amber-500' : 'text-zinc-400')) }}">
                                        {{ $badge['icon'] }}
                                    </span>
                                    <span>{{ $badge['label'] }}</span>
                                </div>
                                <div class="text-[11px] text-zinc-600 dark:text-zinc-300">
                                    @if($invoice->status === 'paid')
                                        Lunas Terverifikasi &bull; Petugas: <strong>{{ $invoice->verifier?->name ?? 'Admin Finance' }}</strong>
                                    @elseif($invoice->status === 'partially_paid')
                                        DP Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }} Terverifikasi Sah &bull; Dicatat: <strong>{{ $invoice->verifier?->name ?? 'Admin Finance (CRM)' }}</strong>
                                    @elseif($invoice->status === 'verifying')
                                        <span class="text-amber-600 dark:text-amber-400 font-bold">Bukti Klien Menunggu Persetujuan</span>
                                    @else
                                        Menunggu Pembayaran Klien
                                    @endif
                                </div>
                                <div class="text-[10px] text-zinc-400 font-mono">
                                    @if($invoice->verified_at && in_array($invoice->status, ['paid', 'partially_paid']))
                                        Diverifikasi: {{ $invoice->verified_at->translatedFormat('d F Y, H:i') }} WIB
                                    @else
                                        Audit: Sistem Terintegrasi VexaHost Admin
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Client View: Payment Instructions: Bank & QRIS -->
                    <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs p-5 sm:p-6">
                        <h3 class="text-sm font-bold text-zinc-900 dark:text-white flex items-center gap-2 mb-4">
                            <span class="material-symbols-outlined text-emerald-600">account_balance</span>
                            <span>Metode Pembayaran Resmi</span>
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 items-center">
                            <!-- Rekening Bank BCA -->
                            <div class="p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-800/50 space-y-2">
                                <div class="text-[11px] font-mono font-bold text-zinc-400 uppercase tracking-wider">Transfer Bank Resmi</div>
                                <div class="text-sm font-black text-zinc-900 dark:text-white">{{ $settings->bank_name ?? '-' }}</div>
                                <div class="flex items-center justify-between p-2.5 rounded-lg bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-700">
                                    <span class="font-mono font-black text-base text-zinc-900 dark:text-white tracking-wider" id="bca-account">
                                        {{ $settings->bank_account_number ?? '-' }}
                                    </span>
                                    <button type="button" onclick="navigator.clipboard.writeText('{{ $settings->bank_account_number ?? '-' }}'); VhSwal.success('Nomor Rekening {{ $settings->bank_name ?? 'BCA' }} berhasil disalin!');" class="p-1 text-zinc-400 hover:text-emerald-600 transition-colors" title="Salin Nomor Rekening">
                                        <span class="material-symbols-outlined text-[18px]">content_copy</span>
                                    </button>
                                </div>
                                <div class="text-xs text-zinc-600 dark:text-zinc-300">
                                    Atas Nama: <strong>{{ $settings->bank_account_holder ?? '-' }}</strong>
                                </div>
                            </div>

                            <!-- Barcode QRIS Resmi -->
                            <div class="p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/70 dark:bg-zinc-800/50 flex flex-col sm:flex-row items-start sm:items-center gap-4">
                                <a href="{{ $settings->qris_url }}" target="_blank" class="shrink-0 p-1.5 bg-white rounded-xl border border-zinc-300 shadow-xs hover:shadow-md transition-shadow group relative block cursor-zoom-in" title="Klik untuk memperbesar atau unduh Barcode QRIS">
                                    <img src="{{ $settings->qris_url }}" 
                                         alt="QRIS Resmi VexaHost" 
                                         class="w-24 h-24 object-contain rounded-lg"
                                         onerror="this.onerror=null; this.src='/images/qris.jpg';">
                                    <div class="absolute inset-0 bg-black/10 opacity-0 group-hover:opacity-100 rounded-xl flex items-center justify-center transition-opacity">
                                        <span class="material-symbols-outlined text-zinc-900 bg-white/90 rounded-full p-1 text-[16px] shadow">zoom_in</span>
                                    </div>
                                </a>
                                <div class="space-y-1">
                                    <div class="text-[11px] font-mono font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Scan QRIS Resmi</div>
                                    <div class="text-xs font-bold text-zinc-900 dark:text-white leading-snug">
                                        Mendukung Seluruh E-Wallet &amp; M-Banking
                                    </div>
                                    <p class="text-[11px] text-zinc-500 leading-tight">
                                        BCA Mobile, Livin Mandiri, GoPay, OVO, ShopeePay, Dana, dll.
                                    </p>
                                    <div class="pt-1">
                                        <a href="{{ $settings->qris_url }}" target="_blank" download="QRIS-VexaHost-Digital-Creative.jpg" class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 hover:text-emerald-700 hover:underline">
                                            <span class="material-symbols-outlined text-[14px]">download</span>
                                            <span>Unduh Barcode QRIS</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>

            <!-- Right Column: Differentiated Panel (Admin Desk vs Client Confirmation) -->
            <div class="space-y-6">
                
                @if($isAdmin)
                    <!-- ========================================== -->
                    <!-- ADMIN PERSPECTIVE: Finance Verification Box -->
                    <!-- ========================================== -->
                    <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs p-5 space-y-5">
                        <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3">
                            <h3 class="text-sm font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                                <span class="material-symbols-outlined text-emerald-600">verified_user</span>
                                <span>Pusat Verifikasi Finance</span>
                            </h3>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-zinc-900 text-white dark:bg-white dark:text-zinc-900">
                                ADMIN
                            </span>
                        </div>

                        <!-- Info Klien / Pembayar -->
                        <div class="p-3.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/60 dark:bg-zinc-800/40 space-y-2">
                            <div class="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider">Informasi Klien:</div>
                            <div class="text-xs font-bold text-zinc-900 dark:text-white">
                                {{ $invoice->client?->name ?? 'Klien' }}
                            </div>
                            <div class="text-[11px] text-zinc-500 space-y-0.5">
                                <div>Email: <span class="font-mono text-zinc-700 dark:text-zinc-300">{{ $invoice->client?->email ?? '-' }}</span></div>
                                <div>WA/Telp: <span class="font-mono text-zinc-700 dark:text-zinc-300">{{ $invoice->client?->phone ?? '-' }}</span></div>
                            </div>
                            @if(!empty($invoice->client?->phone))
                                <div class="pt-1">
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $invoice->client->phone) }}" target="_blank" class="inline-flex items-center gap-1.5 text-[11px] font-bold text-emerald-600 hover:underline">
                                        <span class="material-symbols-outlined text-[15px]">chat</span>
                                        <span>Hubungi Klien via WhatsApp</span>
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Status Tagihan Saat Ini -->
                        @if($invoice->status === 'paid')
                            <div class="p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs text-emerald-800 dark:text-emerald-300 flex items-start gap-2.5">
                                <span class="material-symbols-outlined text-[20px] text-emerald-600 shrink-0">check_circle</span>
                                <div>
                                    <span class="font-bold">Tagihan Telah Lunas Penuh</span>
                                    <p class="text-[11px] mt-0.5">Seluruh tagihan telah diverifikasi lunas. Kwitansi resmi telah aktif dan sah digunakan sebagai bukti pembukuan.</p>
                                </div>
                            </div>
                        @elseif($invoice->status === 'partially_paid')
                            <div class="p-3.5 rounded-xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800 text-xs text-blue-900 dark:text-blue-200 space-y-2">
                                <div class="font-bold flex items-center gap-1.5 text-blue-800 dark:text-blue-300">
                                    <span class="material-symbols-outlined text-[18px]">verified</span>
                                    <span>Uang Muka (DP) Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }} Sah &amp; Terverifikasi</span>
                                </div>
                                <p class="text-[11px] text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                    DP telah dicatat langsung di CRM oleh <strong>{{ $invoice->verifier?->name ?? 'Admin Finance' }}</strong>. Klien tidak perlu konfirmasi ulang untuk pembayaran ini.
                                </p>
                                <div class="pt-1 text-[11px] font-bold text-rose-600 dark:text-rose-400">
                                    Status Proyek: Menunggu Pelunasan Sisa Tagihan (Rp {{ number_format($invoice->balance_due, 0, ',', '.') }})
                                </div>
                                <div class="pt-2 border-t border-blue-200/80 dark:border-blue-800/60">
                                    <a href="{{ route('invoices.receipt', $invoice) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-700 dark:text-blue-300 hover:underline">
                                        <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                                        <span>Buka Lembar Kwitansi Resmi DP &rarr;</span>
                                    </a>
                                </div>
                            </div>
                        @elseif($invoice->status === 'verifying')
                            <div class="p-3.5 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-xs text-amber-800 dark:text-amber-300 flex items-start gap-2.5">
                                <span class="material-symbols-outlined text-[22px] text-amber-600 shrink-0">mark_email_unread</span>
                                <div>
                                    <span class="font-bold">🚨 Klien Telah Mengunggah Bukti Transfer!</span>
                                    <p class="text-[11px] mt-0.5">Silakan periksa mutasi rekening sebelum menyetujui pelunasan tagihan ini.</p>
                                </div>
                            </div>
                        @else
                            <div class="p-3.5 rounded-xl bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-xs text-zinc-700 dark:text-zinc-300 flex items-start gap-2.5">
                                <span class="material-symbols-outlined text-[20px] text-zinc-400 shrink-0">pending</span>
                                <div>
                                    <span class="font-bold">Menunggu Pembayaran Klien</span>
                                    <p class="text-[11px] mt-0.5">Klien belum mengonfirmasi transfer atau mengunggah struk pembayaran melalui portal.</p>
                                </div>
                            </div>
                        @endif

                        <!-- Bukti Pembayaran Klien (Jika Ada) -->
                        @if($invoice->payment_proof)
                            <div class="p-3.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30 space-y-2.5">
                                <span class="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider block">Struk / Bukti Transfer Klien:</span>
                                @php
                                    $isPdfProof = str_ends_with(strtolower($invoice->payment_proof), '.pdf');
                                @endphp
                                @if($isPdfProof)
                                    <a href="{{ Storage::url($invoice->payment_proof) }}" target="_blank" class="inline-flex items-center gap-2 text-xs font-bold text-emerald-600 hover:underline">
                                        <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span>
                                        <span>Buka Dokumen PDF Bukti Transfer</span>
                                    </a>
                                @else
                                    <a href="{{ Storage::url($invoice->payment_proof) }}" target="_blank" class="block group overflow-hidden rounded-lg border border-zinc-300 dark:border-zinc-700">
                                        <img src="{{ Storage::url($invoice->payment_proof) }}" alt="Bukti Transfer" class="w-full h-44 object-cover group-hover:scale-105 transition-transform duration-200">
                                    </a>
                                @endif
                                @if($invoice->payment_notes)
                                    <div class="text-[11px] text-zinc-600 dark:text-zinc-400 italic bg-white dark:bg-zinc-900 p-2 rounded border border-zinc-200 dark:border-zinc-800">
                                        Catatan Klien: "{{ $invoice->payment_notes }}"
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Aksi Tombol Admin -->
                        <div class="pt-2 border-t border-zinc-200/80 dark:border-zinc-800 space-y-2">
                            @if($invoice->status === 'verifying')
                                <!-- Ketika Klien Mengunggah Bukti: Verifikasi DP vs LUNAS -->
                                @php
                                    $defaultDp = $invoice->payment_amount_transferred ?: round($invoice->amount / 2);
                                @endphp
                                <div x-data="{ 
                                    verifyType: '{{ $invoice->payment_type === 'dp' ? 'dp' : ($invoice->paid_amount > 0 ? 'full' : 'dp') }}',
                                    dpAmount: {{ $defaultDp }},
                                    totalAmount: {{ $invoice->amount }},
                                    get balanceAfterDp() { return Math.max(0, this.totalAmount - this.dpAmount); }
                                }" class="space-y-3">
                                    <div class="text-xs font-bold text-zinc-900 dark:text-white flex items-center justify-between">
                                        <span>Tentukan Persetujuan Finance:</span>
                                        <span class="text-[10px] px-2 py-0.5 rounded font-bold uppercase {{ $invoice->payment_type === 'dp' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300' }}">
                                            Klaim: {{ $invoice->payment_type === 'dp' ? 'Uang Muka (DP)' : 'Pelunasan Penuh' }}
                                        </span>
                                    </div>

                                    <!-- Mode Toggle -->
                                    <div class="grid grid-cols-2 gap-2 p-1 bg-zinc-100 dark:bg-zinc-800 rounded-xl">
                                        <button type="button" @click="verifyType = 'dp'" :class="verifyType === 'dp' ? 'bg-white dark:bg-zinc-700 text-blue-600 dark:text-blue-400 shadow-xs font-bold' : 'text-zinc-500 font-medium'" class="py-1.5 px-2 rounded-lg text-xs transition-all text-center">
                                            Setujui sbg DP
                                        </button>
                                        <button type="button" @click="verifyType = 'full'" :class="verifyType === 'full' ? 'bg-white dark:bg-zinc-700 text-emerald-600 dark:text-emerald-400 shadow-xs font-bold' : 'text-zinc-500 font-medium'" class="py-1.5 px-2 rounded-lg text-xs transition-all text-center">
                                            Setujui sbg LUNAS
                                        </button>
                                    </div>

                                    <!-- Mode A: Setujui sebagai DP -->
                                    <form x-show="verifyType === 'dp'" x-ref="formApproveDp" action="{{ route('invoices.verify', $invoice->id) }}" method="POST" class="space-y-3">
                                        @csrf
                                        <input type="hidden" name="action" value="approve_dp">
                                        <div>
                                            <label class="block text-[11px] font-medium text-zinc-500 mb-1">Nominal Uang Muka (DP) yang Diterima:</label>
                                            <div class="relative">
                                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-zinc-400">Rp</span>
                                                <input type="number" name="dp_amount" x-model.number="dpAmount" step="any" min="0" class="w-full text-xs font-bold rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 pl-9 py-1.5 pr-3 focus:ring-blue-500 focus:border-blue-500">
                                            </div>
                                        </div>

                                        <div class="p-2.5 rounded-lg bg-blue-50/70 dark:bg-blue-950/30 border border-blue-200/60 dark:border-blue-800/40 text-[11px] space-y-1">
                                            <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                                                <span>Total Proyek:</span>
                                                <span class="font-mono font-bold">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="flex justify-between text-blue-700 dark:text-blue-300 font-bold">
                                                <span>DP Diterima:</span>
                                                <span class="font-mono">Rp <span x-text="Number(dpAmount).toLocaleString('id-ID')"></span></span>
                                            </div>
                                            <div class="flex justify-between text-rose-600 dark:text-rose-400 font-bold pt-1 border-t border-blue-200 dark:border-blue-800">
                                                <span>Sisa Tagihan Pelunasan:</span>
                                                <span class="font-mono">Rp <span x-text="Number(balanceAfterDp).toLocaleString('id-ID')"></span></span>
                                            </div>
                                        </div>

                                        <button type="button" @click="VhSwal.confirm('Konfirmasi penerimaan Uang Muka (DP) ini? Status tagihan menjadi DP Diterima (Partially Paid) dan saldo sisa tagihan tetap aktif.', () => $refs.formApproveDp.submit())" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-all shadow-xs">
                                            <span class="material-symbols-outlined text-[18px]">verified</span>
                                            <span>Konfirmasi Penerimaan DP</span>
                                        </button>
                                    </form>

                                    <!-- Mode B: Setujui sebagai LUNAS -->
                                    <form x-show="verifyType === 'full'" x-ref="formApproveFull" action="{{ route('invoices.verify', $invoice->id) }}" method="POST" class="space-y-3">
                                        @csrf
                                        <input type="hidden" name="action" value="approve_full">
                                        <div class="p-2.5 rounded-lg bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200/60 dark:border-emerald-800/40 text-[11px] space-y-1">
                                            <div class="flex justify-between text-emerald-800 dark:text-emerald-300 font-bold">
                                                <span>Status Baru:</span>
                                                <span>LUNAS PENUH (100%)</span>
                                            </div>
                                            <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                                                <span>Sisa Tagihan:</span>
                                                <span class="font-mono font-bold text-emerald-600">Rp 0</span>
                                            </div>
                                        </div>
                                        <button type="button" @click="VhSwal.confirm('Konfirmasi tagihan ini sebagai LUNAS 100% dan kirim kwitansi lunas ke WhatsApp klien?', () => $refs.formApproveFull.submit())" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all shadow-xs">
                                            <span class="material-symbols-outlined text-[18px]">task_alt</span>
                                            <span>Verifikasi Pembayaran LUNAS</span>
                                        </button>
                                    </form>

                                    <!-- Reject Form -->
                                    <form x-ref="formRejectProof" action="{{ route('invoices.verify', $invoice->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="action" value="reject">
                                        <button type="button" @click="VhSwal.confirm('Tolak bukti transfer ini dan minta klien upload ulang?', () => $refs.formRejectProof.submit())" class="w-full inline-flex items-center justify-center gap-2 px-4 py-1.5 rounded-xl text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-xs font-bold transition-all">
                                            <span class="material-symbols-outlined text-[16px]">close</span>
                                            <span>Tolak Bukti Transfer</span>
                                        </button>
                                    </form>
                                </div>
                            @elseif($invoice->status === 'partially_paid')
                                <!-- Ketika DP Sudah Tercatat tapi Klien Belum Upload Pelunasan -->
                                <div class="p-3.5 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700 text-center space-y-2.5">
                                    <span class="material-symbols-outlined text-[22px] text-zinc-400 block mx-auto">schedule</span>
                                    <div class="text-xs font-bold text-zinc-800 dark:text-zinc-200">Menunggu Pelunasan dari Klien</div>
                                    <p class="text-[11px] text-zinc-500">
                                        Tombol persetujuan akan otomatis aktif ketika klien mengunggah struk pelunasan (Rp {{ number_format($invoice->balance_due, 0, ',', '.') }}) via portal.
                                    </p>
                                    <div class="pt-2 border-t border-zinc-200 dark:border-zinc-700">
                                        <a href="{{ route('invoices.receipt', $invoice) }}" target="_blank" class="w-full py-2.5 px-3 rounded-xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800 hover:border-blue-500 text-blue-700 dark:text-blue-300 font-bold text-xs inline-flex items-center justify-center gap-2 transition-all shadow-xs">
                                            <span class="material-symbols-outlined text-[18px] text-blue-600">receipt_long</span>
                                            <span>Lihat Kwitansi Resmi DP (Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }})</span>
                                        </a>
                                    </div>
                                </div>

                                <form x-data x-ref="formManualSettlement" action="{{ route('invoices.verify', $invoice->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="action" value="approve">
                                    <button type="button" @click="VhSwal.confirm('Tandai pelunasan sisa tagihan Rp {{ number_format($invoice->balance_due, 0, ',', '.') }} ini secara manual (misal klien bayar tunai/offline langsung) dan kirim kwitansi lunas ke WhatsApp klien?', () => $refs.formManualSettlement.submit())" class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-semibold transition-all border border-zinc-200 dark:border-zinc-700">
                                        <span class="material-symbols-outlined text-[16px]">done_all</span>
                                        <span>Tandai Sisa Tagihan Lunas Manual (Offline)</span>
                                    </button>
                                </form>
                            @elseif($invoice->status === 'unpaid')
                                <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700 text-center space-y-1">
                                    <span class="material-symbols-outlined text-[20px] text-zinc-400 block mx-auto">pending</span>
                                    <div class="text-xs font-bold text-zinc-800 dark:text-zinc-200">Klien Belum Mengirimkan Bukti</div>
                                    <p class="text-[11px] text-zinc-500">
                                        Menunggu klien melakukan transfer atau mengunggah bukti bayar melalui portal.
                                    </p>
                                </div>

                                <form x-data x-ref="formManualPaid" action="{{ route('invoices.verify', $invoice->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="action" value="approve">
                                    <button type="button" @click="VhSwal.confirm('Tandai tagihan Rp {{ number_format($invoice->amount, 0, ',', '.') }} ini LUNAS secara manual (misal klien bayar tunai langsung) dan kirim kwitansi lunas ke WhatsApp klien?', () => $refs.formManualPaid.submit())" class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-semibold transition-all border border-zinc-200 dark:border-zinc-700">
                                        <span class="material-symbols-outlined text-[16px]">done_all</span>
                                        <span>Tandai Lunas Manual (Offline)</span>
                                    </button>
                                </form>
                            @elseif($invoice->status === 'paid')
                                <a href="{{ route('invoices.receipt', $invoice) }}" target="_blank" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-bold transition-all hover:bg-emerald-100">
                                    <span class="material-symbols-outlined text-[20px] text-emerald-600">receipt_long</span>
                                    <span>Lihat &amp; Cetak Kwitansi Resmi Pelunasan</span>
                                </a>
                            @endif
                        </div>
                    </div>

                @else
                    <!-- ========================================== -->
                    <!-- CLIENT PERSPECTIVE: Payment Confirmation   -->
                    <!-- ========================================== -->
                    <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200/80 dark:border-zinc-800 shadow-xs p-5 space-y-4">
                        <h3 class="text-sm font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-emerald-600">verified</span>
                            <span>{{ $invoice->status === 'partially_paid' ? 'Pelunasan Sisa Tagihan' : 'Konfirmasi Pembayaran' }}</span>
                        </h3>

                        <!-- Status Banner Klien -->
                        @if($invoice->status === 'paid')
                            <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs text-emerald-800 dark:text-emerald-300 space-y-3">
                                <div class="flex items-start gap-2.5">
                                    <span class="material-symbols-outlined text-[22px] text-emerald-600 shrink-0">check_circle</span>
                                    <div>
                                        <span class="font-bold text-sm">Tagihan Sudah Lunas Penuh!</span>
                                        <p class="text-[11px] mt-0.5 text-zinc-600 dark:text-zinc-400">Terima kasih banyak atas pelunasan pembayaran Anda. Seluruh berkas pengerjaan proyek aktif berjalan.</p>
                                    </div>
                                </div>
                                <div class="pt-2 border-t border-emerald-200/80 dark:border-emerald-800/60">
                                    <a href="{{ route('invoices.receipt', $invoice) }}" target="_blank" class="w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm inline-flex items-center justify-center gap-2.5 shadow-md hover:shadow-lg transition-all active:scale-[0.98]">
                                        <span class="material-symbols-outlined text-[22px]">receipt_long</span>
                                        <span>Lihat &amp; Cetak Kwitansi Resmi Pelunasan</span>
                                    </a>
                                </div>
                            </div>
                        @elseif($invoice->status === 'partially_paid')
                            <div class="p-4 rounded-xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800 text-xs text-blue-900 dark:text-blue-200 space-y-3">
                                <div class="flex items-start gap-2.5">
                                    <span class="material-symbols-outlined text-[22px] text-blue-600 shrink-0">payments</span>
                                    <div>
                                        <div class="font-bold text-sm">Uang Muka (DP) Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }} Sah &amp; Terverifikasi</div>
                                        <p class="text-[11px] mt-1 text-zinc-600 dark:text-zinc-400 leading-relaxed">
                                            Pembayaran DP telah dinyatakan sah oleh Tim Finance. Anda <strong>tidak perlu mengonfirmasi DP ini lagi</strong>.
                                        </p>
                                        <div class="mt-2 text-rose-600 dark:text-rose-400 font-bold text-xs">
                                            Sisa Tagihan Pelunasan: Rp {{ number_format($invoice->balance_due, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Tombol Kwitansi Resmi DP (Besar & Jelas Terlihat) -->
                                <div class="pt-2.5 border-t border-blue-200/80 dark:border-blue-800/60">
                                    <a href="{{ route('invoices.receipt', $invoice) }}" target="_blank" class="w-full py-3.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm inline-flex items-center justify-center gap-2.5 shadow-md hover:shadow-lg transition-all active:scale-[0.98] cursor-pointer">
                                        <span class="material-symbols-outlined text-[24px]">receipt_long</span>
                                        <span>Lihat &amp; Cetak Kwitansi Resmi DP (Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }})</span>
                                    </a>
                                </div>
                            </div>
                        @elseif($invoice->status === 'verifying')
                            <div class="p-3.5 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-xs text-amber-800 dark:text-amber-300 flex items-start gap-2.5">
                                <span class="material-symbols-outlined text-[18px] text-amber-600 shrink-0">hourglass_top</span>
                                <div>
                                    <span class="font-bold">Sedang Diverifikasi</span>
                                    <p class="text-[11px] mt-0.5">Bukti transfer Anda telah diterima sistem dan sedang diperiksa oleh Tim Finance. Notifikasi WhatsApp akan terkirim segera setelah verifikasi selesai.</p>
                                </div>
                            </div>
                        @endif

                        <!-- Uploaded Proof Preview if Exists -->
                        @if($invoice->payment_proof)
                            <div class="p-3 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30 space-y-2">
                                <span class="text-[11px] font-mono font-bold text-zinc-400 uppercase tracking-wider block">Bukti Transfer Terunggah:</span>
                                @php
                                    $isPdfProof = str_ends_with(strtolower($invoice->payment_proof), '.pdf');
                                @endphp
                                @if($isPdfProof)
                                    <a href="{{ Storage::url($invoice->payment_proof) }}" target="_blank" class="inline-flex items-center gap-2 text-xs font-bold text-emerald-600 hover:underline">
                                        <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span>
                                        <span>Buka File Bukti PDF</span>
                                    </a>
                                @else
                                    <a href="{{ Storage::url($invoice->payment_proof) }}" target="_blank" class="block group overflow-hidden rounded-lg border border-zinc-300 dark:border-zinc-700">
                                        <img src="{{ Storage::url($invoice->payment_proof) }}" alt="Bukti Transfer" class="w-full h-40 object-cover group-hover:scale-105 transition-transform duration-200">
                                    </a>
                                @endif
                                @if($invoice->payment_notes)
                                    <div class="text-[11px] text-zinc-600 dark:text-zinc-400 italic">
                                        Catatan: "{{ $invoice->payment_notes }}"
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Upload Form for Client (If not paid) -->
                        @if($invoice->status !== 'paid')
                            <div class="pt-2 border-t border-zinc-200/80 dark:border-zinc-800 space-y-3">
                                @if($invoice->status === 'partially_paid')
                                    <!-- Jika DP sudah sah, form khusus pelunasan sisa -->
                                    <div class="text-xs font-bold text-zinc-800 dark:text-zinc-200">
                                        {{ $invoice->payment_proof ? 'Unggah Bukti Pelunasan Baru (Re-upload):' : 'Unggah Bukti Pelunasan Sisa Tagihan (Rp ' . number_format($invoice->balance_due, 0, ',', '.') . '):' }}
                                    </div>
                                    <form action="{{ route('invoices.upload-proof', $invoice->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                        @csrf
                                        <input type="hidden" name="payment_type" value="full">
                                        <input type="hidden" name="payment_amount_transferred" value="{{ $invoice->balance_due }}">
                                        <div>
                                            <label class="block text-[11px] text-zinc-500 mb-1">Pilih File Bukti Pelunasan (JPG, PNG, PDF maks 5MB)</label>
                                            <input type="file" name="payment_proof" required accept=".jpg,.jpeg,.png,.pdf" class="block w-full text-xs text-zinc-700 dark:text-zinc-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-zinc-100 dark:file:bg-zinc-800 file:text-zinc-700 dark:file:text-zinc-300 hover:file:bg-zinc-200 cursor-pointer">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] text-zinc-500 mb-1">Catatan / Nama Pemilik Rekening Pengirim</label>
                                            <input type="text" name="payment_notes" placeholder="Contoh: Pelunasan Sisa via BCA an Bpk. Budi" class="w-full text-xs rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 py-1.5 px-3 focus:ring-emerald-500 focus:border-emerald-500">
                                        </div>
                                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all shadow-xs">
                                            <span class="material-symbols-outlined text-[18px]">upload</span>
                                            <span>Kirim Bukti Pelunasan (Rp {{ number_format($invoice->balance_due, 0, ',', '.') }})</span>
                                        </button>
                                    </form>
                                @else
                                    <!-- Jika belum bayar sama sekali, klien cukup pilih apakah transfer DP atau Pelunasan Penuh -->
                                    <div x-data="{ clientPayType: '{{ $invoice->payment_type ?: 'dp' }}' }" class="space-y-3">
                                        <div>
                                            <label class="block text-[11px] font-bold text-zinc-700 dark:text-zinc-300 mb-1.5">
                                                Pilih Jenis Pembayaran yang Anda Lakukan:
                                            </label>
                                            <div class="grid grid-cols-2 gap-2">
                                                <button type="button" @click="clientPayType = 'dp'" 
                                                        :class="clientPayType === 'dp' ? 'border-blue-600 bg-blue-50 dark:bg-blue-950/40 text-blue-800 dark:text-blue-300 font-bold ring-2 ring-blue-500/20' : 'border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400'" 
                                                        class="border rounded-xl py-2 px-3 text-center transition-all cursor-pointer">
                                                    <div class="text-xs">Uang Muka (DP)</div>
                                                    <div class="text-[10px] text-zinc-400 font-normal mt-0.5">Mulai pengerjaan proyek</div>
                                                </button>
                                                <button type="button" @click="clientPayType = 'full'" 
                                                        :class="clientPayType === 'full' ? 'border-emerald-600 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 font-bold ring-2 ring-emerald-500/20' : 'border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400'" 
                                                        class="border rounded-xl py-2 px-3 text-center transition-all cursor-pointer">
                                                    <div class="text-xs">Pelunasan Penuh</div>
                                                    <div class="text-[10px] text-zinc-400 font-normal mt-0.5">Lunas 100% langsung</div>
                                                </button>
                                            </div>
                                        </div>

                                        <form action="{{ route('invoices.upload-proof', $invoice->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                            @csrf
                                            <input type="hidden" name="payment_type" :value="clientPayType">

                                            <div>
                                                <label class="block text-[11px] text-zinc-500 mb-1 font-medium">Unggah Bukti Transfer / Struk QRIS (JPG, PNG, PDF maks 5MB)</label>
                                                <input type="file" name="payment_proof" required accept=".jpg,.jpeg,.png,.pdf" class="block w-full text-xs text-zinc-700 dark:text-zinc-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-zinc-100 dark:file:bg-zinc-800 file:text-zinc-700 dark:file:text-zinc-300 hover:file:bg-zinc-200 cursor-pointer">
                                            </div>

                                            <div>
                                                <label class="block text-[11px] text-zinc-500 mb-1 font-medium">Catatan / Nama Pemilik Rekening Pengirim <span class="text-zinc-400 font-normal">(Opsional)</span></label>
                                                <input type="text" name="payment_notes" placeholder="Contoh: Transfer via BCA an Bpk. Budi" class="w-full text-xs rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 py-2 px-3 focus:ring-emerald-500 focus:border-emerald-500">
                                            </div>

                                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-white text-xs font-bold transition-all shadow-xs cursor-pointer" :class="clientPayType === 'dp' ? 'bg-blue-600 hover:bg-blue-700' : 'bg-emerald-600 hover:bg-emerald-700'">
                                                <span class="material-symbols-outlined text-[18px]">upload</span>
                                                <span x-text="clientPayType === 'dp' ? 'Kirim Bukti Pembayaran DP' : 'Kirim Bukti Pelunasan Penuh'"></span>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Quick WhatsApp Link -->
                        <div class="pt-3 border-t border-zinc-200/80 dark:border-zinc-800 text-center">
                            <p class="text-[11px] text-zinc-400 mb-2">Ingin konfirmasi langsung tanpa upload form?</p>
                            <a href="https://wa.me/{{ $waPhone }}?text={{ $waShareText }}" target="_blank" class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-xl bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-xs font-bold transition-all">
                                <span class="material-symbols-outlined text-[18px] text-emerald-600">send</span>
                                <span>Kirim Bukti via WhatsApp</span>
                            </a>
                        </div>
                    </div>
                @endif

            </div>

        </div>

    </div>
</x-app-layout>

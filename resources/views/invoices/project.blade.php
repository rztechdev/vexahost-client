<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoiceNumber }} - {{ $project->nama_project }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 15mm 15mm;
        }
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #111111;
            background-color: {{ ($isPdf ?? false) ? '#ffffff' : '#f4f4f5' }};
            margin: 0;
            padding: {{ ($isPdf ?? false) ? '0' : '20px' }};
        }
        .no-print {
            {{ ($isPdf ?? false) ? 'display: none !important;' : '' }}
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background-color: #ffffff !important;
                padding: 0 !important;
            }
            .invoice-wrapper {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                max-width: 100% !important;
            }
        }
        .action-bar {
            max-width: 800px;
            margin: 0 auto 16px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 12px 18px;
            border-radius: 8px;
            border: 1px solid #e4e4e7;
        }
        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid #18181b;
            background: #18181b;
            color: #ffffff;
        }
        .action-btn.secondary {
            background: #ffffff;
            color: #18181b;
            border: 1px solid #d4d4d8;
        }
        .action-btn.secondary:hover {
            background: #f4f4f5;
        }
        .flash-alert {
            max-width: 800px;
            margin: 0 auto 16px auto;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid #000000;
            background: #f4f4f5;
            color: #000000;
        }

        /* Invoice Container */
        .invoice-wrapper {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            padding: {{ ($isPdf ?? false) ? '0' : '40px 45px' }};
            border: {{ ($isPdf ?? false) ? 'none' : '1px solid #e4e4e7' }};
            box-shadow: {{ ($isPdf ?? false) ? 'none' : '0 4px 15px rgba(0,0,0,0.06)' }};
            border-radius: {{ ($isPdf ?? false) ? '0' : '4px' }};
        }

        /* Tables & Structure */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: top;
            padding-bottom: 15px;
        }
        .divider {
            border-top: 2px solid #000000;
            margin: 5px 0 18px 0;
        }
        .meta-table td {
            vertical-align: top;
            padding: 4px 0;
            font-size: 10.5pt;
        }
        .items-table {
            margin-top: 15px;
            margin-bottom: 15px;
        }
        .items-table th {
            background-color: #f4f4f5;
            color: #000000;
            font-weight: 700;
            font-size: 9.5pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 10px;
            border-top: 1.5px solid #000000;
            border-bottom: 1.5px solid #000000;
            text-align: left;
        }
        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #e4e4e7;
            font-size: 10pt;
            vertical-align: top;
        }
        .items-table .text-right {
            text-align: right;
        }
        .items-table .text-center {
            text-align: center;
        }
        .summary-table {
            width: 100%;
            margin-top: 10px;
        }
        .summary-table td {
            padding: 4px 0;
            font-size: 10.5pt;
        }
        .total-due {
            border-top: 1.5px solid #000000;
            border-bottom: 2px solid #000000;
            padding-top: 6px !important;
            padding-bottom: 6px !important;
            font-size: 11.5pt !important;
            font-weight: bold;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border: 1px solid #000000;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .payment-box {
            border: 1px solid #000000;
            padding: 12px;
            margin-top: 15px;
            background: #fafafa;
        }
        .qris-img {
            width: 95px;
            height: 95px;
            border: 1px solid #000000;
            padding: 2px;
            background: #ffffff;
        }
        .footer-note {
            font-size: 8.5pt;
            color: #444444;
            line-height: 1.4;
            margin-top: 15px;
            border-top: 1px dashed #cccccc;
            padding-top: 10px;
        }
        .sign-table {
            margin-top: 25px;
            width: 100%;
        }
        .sign-table td {
            vertical-align: top;
            font-size: 9.5pt;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

@if(!($isPdf ?? false))
    <!-- Action Bar & Notifications (Web View Only) -->
    @if(session('success'))
        <div class="flash-alert no-print">
            [✔] {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flash-alert no-print" style="border-color: #000; background: #fff1f2;">
            [✕] {{ session('error') }}
        </div>
    @endif

    <div class="action-bar no-print">
        <div>
            <a href="{{ $project->id ? route('projects.show', $project->id) : '#' }}" class="action-btn secondary">
                &larr; Kembali ke Detail Proyek
            </a>
        </div>
        <div style="display: flex; gap: 8px;">
            @php
                $waShareText = rawurlencode("Halo Kak {$lead?->nama_kontak},\n\nBerikut rincian Invoice resmi untuk proyek *{$project->nama_project}*:\nNomor: {$invoiceNumber}\nTotal: Rp " . number_format($project->harga, 0, ',', '.') . "\nSisa Tagihan: Rp " . number_format($project->sisa_tagihan, 0, ',', '.') . "\n\nTerima kasih atas kerja samanya!\n- VexaHost");
                $waPhone = preg_replace('/[^0-9]/', '', $lead?->kontak_wa ?? '');
            @endphp
            @if($project->id)
                <form id="formSendWaProject" action="{{ route('invoices.project.send-wa', $project->id) }}" method="POST" style="display: inline; margin: 0;">
                    @csrf
                    <button type="button" onclick="Swal.fire({ text: 'Kirim dokumen PDF Invoice ini langsung ke nomor WhatsApp {{ $lead?->kontak_wa }} via VexaHost WA Gateway?', icon: 'question', showCancelButton: true, confirmButtonText: 'Ya, Kirim', cancelButtonText: 'Batal', confirmButtonColor: '#C2410C', cancelButtonColor: '#71717a', reverseButtons: true }).then((r) => { if (r.isConfirmed) document.getElementById('formSendWaProject').submit(); })" class="action-btn">
                        Kirim PDF via WhatsApp
                    </button>
                </form>
            @endif
            <a href="https://wa.me/{{ $waPhone }}?text={{ $waShareText }}" target="_blank" class="action-btn secondary">
                Teks WA
            </a>
            <a href="{{ route('invoices.project', ['project' => $project->id ?? 1, 'format' => 'pdf']) }}" class="action-btn secondary">
                Download PDF
            </a>
            <a href="{{ route('invoices.project', ['project' => $project->id ?? 1, 'format' => 'word']) }}" class="action-btn secondary">
                Download Word
            </a>
            <button onclick="window.print()" class="action-btn secondary">
                Cetak
            </button>
        </div>
    </div>
@endif

<!-- Enterprise Invoice Sheet -->
<div class="invoice-wrapper">

    <!-- Header: Company Info & Logo -->
    <table class="header-table">
        <tr>
            <td style="width: 60%;">
                <table style="width: 100%;">
                    <tr>
                        @if(!empty($logoBase64))
                            <td style="width: 65px; vertical-align: middle;">
                                <img src="{{ $logoBase64 }}" alt="Logo" style="height: 55px; width: auto;" />
                            </td>
                        @endif
                        <td style="vertical-align: middle; padding-left: 8px;">
                            <div style="font-size: 13pt; font-weight: 800; letter-spacing: 0.5px; text-transform: uppercase;">
                                {{ $settings->company_name ?? 'PT DESTINARA CHAKRAWALA ARTHA' }}
                            </div>
                            <div style="font-size: 8.5pt; font-weight: 600; color: #444444; text-transform: uppercase; letter-spacing: 0.5px;">
                                {{ $settings->tagline ?? 'Software House & Digital Solutions' }}
                            </div>
                        </td>
                    </tr>
                </table>
                <div style="font-size: 8.5pt; color: #333333; margin-top: 8px; line-height: 1.4;">
                    Email: {{ $settings->email_support ?? 'vexahostcloudtech@gmail.com' }} | {{ $settings->email_company ?? 'vexahostcloudtech@gmail.com' }}<br>
                    Website: {{ $settings->website_url ?? 'https://vexahostcloud.my.id' }}<br>
                    WhatsApp: {{ $settings->phone_support ?? '0858-0874-9131' }}{{ !empty($settings->phone_support_2) ? ' / ' . $settings->phone_support_2 : '' }}
                </div>
            </td>
            <td style="width: 40%; text-align: right; vertical-align: middle;">
                <div style="font-size: 20pt; font-weight: 900; letter-spacing: 1px; text-transform: uppercase;">
                    INVOICE
                </div>
                <div style="font-size: 10.5pt; font-weight: bold; margin-top: 4px;">
                    No: {{ $invoiceNumber }}
                </div>
                <div style="margin-top: 6px;">
                    <span class="status-badge">
                        {{ strtoupper($project->payment_status ?? 'MENUNGGU PEMBAYARAN') }}
                    </span>
                </div>
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    <!-- Metadata: Bill To & Dates -->
    <table class="meta-table" style="margin-bottom: 10px;">
        <tr>
            <td style="width: 58%;">
                <div style="font-size: 8.5pt; font-weight: bold; text-transform: uppercase; color: #555555;">
                    TAGIHAN KEPADA (BILL TO):
                </div>
                <div style="font-size: 12pt; font-weight: bold; margin-top: 3px;">
                    {{ $lead?->nama_usaha ?? 'Klien VexaHost' }}
                </div>
                <div style="font-size: 9.5pt; color: #222222; margin-top: 2px;">
                    U.p: <strong>{{ $lead?->nama_kontak ?? '-' }}</strong>
                </div>
                <div style="font-size: 9pt; color: #444444;">
                    WhatsApp: {{ $lead?->kontak_wa ?? '-' }}<br>
                    Email: {{ $lead?->email ?? '-' }}
                </div>
            </td>
            <td style="width: 42%; text-align: right;">
                <div style="font-size: 8.5pt; font-weight: bold; text-transform: uppercase; color: #555555;">
                    DETAIL PENAGIHAN:
                </div>
                <table style="width: 100%; margin-top: 3px; font-size: 9.5pt;">
                    <tr>
                        <td style="text-align: right; color: #555555; padding: 2px 0;">Tanggal Terbit:</td>
                        <td style="text-align: right; font-weight: bold; width: 110px; padding: 2px 0;">{{ $invoiceDate }}</td>
                    </tr>
                    <tr>
                        <td style="text-align: right; color: #555555; padding: 2px 0;">Jatuh Tempo:</td>
                        <td style="text-align: right; font-weight: bold; padding: 2px 0;">{{ $dueDate }}</td>
                    </tr>
                    <tr>
                        <td style="text-align: right; color: #555555; padding: 2px 0;">Jenis Layanan:</td>
                        <td style="text-align: right; font-weight: bold; padding: 2px 0;">{{ strtoupper($project->paket_label ?? 'Project') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Line Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No.</th>
                <th style="width: 55%;">Deskripsi Layanan / Pekerjaan</th>
                <th style="width: 15%; text-align: center;">Kuantitas</th>
                <th style="width: 25%; text-align: right;">Total Harga (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" style="font-weight: bold;">1</td>
                <td>
                    <div style="font-weight: bold; font-size: 10.5pt;">{{ $project->nama_project }}</div>
                    <div style="font-size: 8.5pt; color: #444444; margin-top: 3px;">
                        Pengerjaan Software / Website Paket {{ $project->paket_label }} mencakup setup cloud hosting, konfigurasi domain, UI/UX responsif, dan integrasi WhatsApp.
                    </div>
                </td>
                <td class="text-center">1 Paket</td>
                <td class="text-right" style="font-weight: bold;">
                    {{ number_format($project->harga, 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Calculation & Payment Breakdown -->
    <table style="width: 100%; margin-top: 5px;">
        <tr>
            <!-- Left: Payment Instructions & QRIS -->
            <td style="width: 55%; vertical-align: top; padding-right: 15px;">
                <div class="payment-box">
                    <table style="width: 100%;">
                        <tr>
                            @if(!empty($qrisBase64))
                                <td style="width: 105px; vertical-align: top; text-align: center;">
                                    <img src="{{ $qrisBase64 }}" alt="QRIS" class="qris-img" />
                                    <div style="font-size: 7.5pt; font-weight: bold; text-transform: uppercase; margin-top: 3px;">
                                        SCAN QRIS RESMI
                                    </div>
                                </td>
                            @endif
                            <td style="vertical-align: top; padding-left: 8px;">
                                <div style="font-size: 8.5pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;">
                                    REKENING PEMBAYARAN RESMI:
                                </div>
                                <div style="font-size: 9.5pt; font-weight: bold; margin-top: 4px;">
                                    {{ $settings->bank_name ?? '-' }}
                                </div>
                                <div style="font-size: 11pt; font-weight: 900; letter-spacing: 0.5px; margin-top: 1px;">
                                    {{ $settings->bank_account_number ?? '-' }}
                                </div>
                                <div style="font-size: 8.5pt; color: #333333; margin-top: 1px;">
                                    a.n {{ $settings->bank_account_holder ?? '-' }}
                                </div>
                                <div style="font-size: 7.5pt; color: #555555; margin-top: 6px; line-height: 1.3;">
                                    Konfirmasi bukti transfer / struk QRIS dapat dikirimkan ke WhatsApp resmi atau diunggah pada Client Panel.
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>

            <!-- Right: Subtotal, DP, and Balance Due -->
            <td style="width: 45%; vertical-align: top;">
                <table class="summary-table">
                    <tr>
                        <td style="color: #444444;">Subtotal Biaya Proyek:</td>
                        <td style="text-align: right; font-weight: bold;">
                            Rp {{ number_format($project->harga, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr>
                        <td style="color: #444444;">Pembayaran Diterima (DP/Mutasi):</td>
                        <td style="text-align: right; font-weight: bold;">
                            - Rp {{ number_format($project->total_terbayar, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr class="total-due">
                        <td>SISA TAGIHAN (DUE):</td>
                        <td style="text-align: right;">
                            Rp {{ number_format($project->sisa_tagihan, 0, ',', '.') }}
                        </td>
                    </tr>
                </table>

                <div style="margin-top: 10px; text-align: right; font-size: 8pt; color: #555555;">
                    @if($project->sisa_tagihan <= 0)
                        Status: <strong>LUNAS - Pembayaran Selesai</strong>
                    @else
                        Status: <strong>Harap diselesaikan sebelum tanggal jatuh tempo</strong>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- Terms and Signatures -->
    <table class="sign-table">
        <tr>
            <td style="width: 60%; vertical-align: bottom;">
                <div class="footer-note">
                    <strong>Syarat &amp; Ketentuan Penagihan:</strong><br>
                    {!! nl2br(e($settings->invoice_terms ?? "1. Pembayaran resmi hanya sah apabila ditransfer ke rekening resmi VexaHost yang tercantum pada dokumen ini.\n2. Kwitansi resmi lunas bertanda tangan digital akan diterbitkan otomatis setelah pembayaran diverifikasi.\n3. Untuk bantuan teknis atau administrasi penagihan, hubungi WhatsApp: 0858-0874-9131.")) !!}
                </div>
            </td>
            <td style="width: 40%; text-align: center; vertical-align: bottom;">
                <div style="font-size: 9pt; color: #444444;">
                    {{ $settings->domicile_city ?? 'Jakarta' }}, {{ $invoiceDate }}
                </div>
                <div style="font-size: 9pt; font-weight: bold; text-transform: uppercase; margin-top: 3px;">
                    {{ $settings->company_name ?? 'PT DESTINARA CHAKRAWALA ARTHA' }}
                </div>
                <div style="height: 55px; margin: 4px 0; text-align: center;">
                    @if(!empty($signatureBase64))
                        <img src="{{ $signatureBase64 }}" alt="Tanda Tangan" style="max-height: 55px; width: auto; max-width: 140px; display: inline-block;" />
                    @endif
                </div>
                <div style="font-size: 9.5pt; font-weight: bold; text-decoration: underline;">
                    {{ $settings->director_name ?? 'Manajemen VexaHost' }}
                </div>
                <div style="font-size: 8pt; color: #555555; text-transform: uppercase;">
                    {{ $settings->director_title ?? 'Finance & Executive Director' }}
                </div>
            </td>
        </tr>
    </table>

</div>

</body>
</html>

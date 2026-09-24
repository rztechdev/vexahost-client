<?php

namespace App\Services\WhatsApp;

use App\Models\Lead;
use App\Models\Project;
use App\Models\MaintenanceSubscription;
use App\Models\ProjectSubscription;
use App\Models\CompanySetting;

class WhatsAppTemplates
{
    /**
     * Helper to get settings instance safely.
     */
    protected static function resolveSettings(?CompanySetting $settings = null): CompanySetting
    {
        return $settings ?: CompanySetting::get();
    }

    /**
     * Template: Konfirmasi DP Diterima & Pengerjaan Dimulai.
     */
    public static function dpReceived(Lead $lead, Project $project, ?string $estimasiSelesai = null, ?CompanySetting $settings = null): string
    {
        $cfg = self::resolveSettings($settings);
        $nama = $lead->nama_kontak ?: $lead->nama_usaha;
        $paket = $project->paket_label;
        $estimasi = $estimasiSelesai ?: ($project->tanggal_selesai ? $project->tanggal_selesai->translatedFormat('d F Y') : '7-14 hari kerja ke depan');
        $brand = $cfg->brand_name ?: 'VexaHost';

        return "Halo Kak {$nama}, DP untuk project *{$paket}* ({$project->nama_project}) sudah kami terima dengan baik. 🙏\n\n"
             . "Project langsung kami masukkan ke antrean pengerjaan ya. Estimasi selesai pengerjaan sekitar *{$estimasi}*.\n\n"
             . "Nanti setiap progres penting akan kami update berkala. Terima kasih banyak atas kepercayaannya bersama {$brand}! ✨";
    }

    /**
     * Template: Project Selesai & Live + Penawaran Maintenance Bulanan.
     */
    public static function projectCompleted(Lead $lead, Project $project, ?string $linkWebsite = null, ?int $hargaMaintenance = null, ?CompanySetting $settings = null): string
    {
        $cfg = self::resolveSettings($settings);
        $nama = $lead->nama_kontak ?: $lead->nama_usaha;
        $link = $linkWebsite ?: $project->link_website;
        $brand = $cfg->brand_name ?: 'VexaHost';
        $maintPrice = number_format($hargaMaintenance ?: config('whatsapp.default_maintenance_price', 150000), 0, ',', '.');
        $linkSection = $link ? "🌐 Link Website: {$link}\n\n" : "";

        return "Halo Kak {$nama}! 🎉\n\n"
             . "Kabar gembira, website untuk *{$project->nama_project}* sudah selesai dan *LIVE* bisa diakses publik!\n\n"
             . $linkSection
             . "Semoga websitenya berkontribusi menaikkan omzet dan kredibilitas usaha Kakak ya. 🚀\n\n"
             . "💡 *Info Pendampingan & Maintenance:*\n"
             . "Biar website tetap aman, cepat, dan kalau butuh update promo/banner tanpa ribet coding, kami ada paket pendampingan bulanan mulai *Rp {$maintPrice}/bulan*. Tinggal chat kami saja jika ingin diaktifkan ya Kak. Terima kasih banyak atas kerjasamanya bersama {$brand}! 🙏✨";
    }

    /**
     * Template: Project Sedang Dikerjakan.
     */
    public static function projectInProgress(Lead $lead, Project $project, ?CompanySetting $settings = null): string
    {
        $cfg = self::resolveSettings($settings);
        $nama = $lead->nama_kontak ?: $lead->nama_usaha;
        $portalUrl = config('app.url', 'https://client.vexahostcloud.my.id');
        $brand = $cfg->brand_name ?: 'VexaHost';

        return "Halo Kak {$nama}! 🚀\n\n"
             . "Update progres proyek *{$project->nama_project}* ({$project->paket_label}):\n"
             . "Status proyek saat ini: *Sedang Dikerjakan*.\n\n"
             . "Tim developer {$brand} telah memulai proses pengerjaan teknis & coding untuk sistem/website Anda. Kakak dapat memantau progres tahapan pengerjaan secara langsung melalui *Client Panel*:\n"
             . "🔗 {$portalUrl}\n\n"
             . "Kami akan mengabari kembali saat proyek siap memasuki tahap review. Terima kasih banyak atas kepercayaannya! 🙏✨";
    }

    /**
     * Template: Project Masuk Tahap Review Klien.
     */
    public static function projectReview(Lead $lead, Project $project, ?CompanySetting $settings = null): string
    {
        $cfg = self::resolveSettings($settings);
        $nama = $lead->nama_kontak ?: $lead->nama_usaha;
        $portalUrl = config('app.url', 'https://client.vexahostcloud.my.id');
        $linkPreview = $project->link_website ? "\n🌐 Link Pratinjau: {$project->link_website}" : '';
        $brand = $cfg->brand_name ?: 'VexaHost';

        return "Halo Kak {$nama}! ✨\n\n"
             . "Kabar baik, pengerjaan proyek *{$project->nama_project}* telah memasuki tahap *Review Klien / Uji Coba Pratinjau*!{$linkPreview}\n\n"
             . "Silakan cek hasil pengerjaan website/aplikasi Anda dan laporkan jika ada revisi atau masukan melalui *Client Panel*:\n"
             . "🔗 {$portalUrl}\n\n"
             . "Tim {$brand} siap menyempurnakan sebelum website resmi diluncurkan (*LIVE*). Terima kasih! 🚀";
    }

    /**
     * Template: Project Dibatalkan.
     */
    public static function projectCancelled(Lead $lead, Project $project, ?CompanySetting $settings = null): string
    {
        $cfg = self::resolveSettings($settings);
        $nama = $lead->nama_kontak ?: $lead->nama_usaha;
        $brand = $cfg->brand_name ?: 'VexaHost';

        return "Halo Kak {$nama},\n\nPemberitahuan bahwa status pengerjaan proyek *{$project->nama_project}* telah diperbarui menjadi *Dibatalkan*. Jika ada pertanyaan atau kebutuhan koordinasi lebih lanjut, silakan hubungi tim support {$brand} di nomor {$cfg->phone_support}. Terima kasih. 🙏";
    }

    /**
     * Template: Update Status Generic Proyek.
     */
    public static function projectStatusUpdated(Lead $lead, Project $project, ?CompanySetting $settings = null): string
    {
        $cfg = self::resolveSettings($settings);
        $nama = $lead->nama_kontak ?: $lead->nama_usaha;
        $portalUrl = config('app.url', 'https://client.vexahostcloud.my.id');
        $brand = $cfg->brand_name ?: 'VexaHost';

        return "Halo Kak {$nama},\n\nStatus pengerjaan proyek *{$project->nama_project}* telah diperbarui menjadi: *{$project->status_label}*.\n\n"
             . "Pantau detail perkembangan proyek Anda melalui *Client Panel*:\n"
             . "🔗 {$portalUrl}\n\n"
             . "Terima kasih atas kerjasamanya bersama {$brand}! 🙏✨";
    }

    /**
     * Template: Pengingat Tagihan Maintenance Bulanan (H-3).
     */
    public static function maintenanceReminder(Lead $lead, MaintenanceSubscription $subscription, ?string $bankInfo = null, ?CompanySetting $settings = null): string
    {
        $cfg = self::resolveSettings($settings);
        $nama = $lead->nama_kontak ?: $lead->nama_usaha;
        $jumlah = number_format($subscription->harga_bulanan, 0, ',', '.');
        $jatuhTempo = $subscription->tanggal_jatuh_tempo_berikutnya ? $subscription->tanggal_jatuh_tempo_berikutnya->translatedFormat('d F Y') : 'akhir bulan ini';
        $infoBank = $bankInfo ?: ($cfg->bank_info_string ?: "{$cfg->bank_name} {$cfg->bank_account_number} a.n {$cfg->bank_account_holder}");
        $brand = $cfg->brand_name ?: 'VexaHost';

        return "Halo Kak {$nama}, pengingat santai untuk tagihan layanan pemeliharaan & maintenance website bulan ini sebesar *Rp {$jumlah}* dengan jatuh tempo tanggal *{$jatuhTempo}*.\n\n"
             . "💳 *Pembayaran dapat ditransfer ke rekening resmi:*\n"
             . "🏦 {$infoBank}\n\n"
             . "Jika sudah transfer, mohon konfirmasi bukti transfernya ke nomor ini ya. Terima kasih banyak atas kerjasamanya bersama {$brand}! 🙏✨";
    }

    /**
     * Template: Tagihan / Instruksi Pelunasan Proyek (Tahap Review menuju Live).
     */
    public static function projectSettlementRequest(Lead $lead, Project $project, ?string $bankInfo = null, ?CompanySetting $settings = null): string
    {
        $cfg = self::resolveSettings($settings);
        $nama = $lead->nama_kontak ?: $lead->nama_usaha;
        $total = number_format($project->harga, 0, ',', '.');
        $terbayar = number_format($project->total_paid, 0, ',', '.');
        $sisa = number_format($project->remaining_balance, 0, ',', '.');
        $infoBank = $bankInfo ?: ($cfg->bank_info_string ?: "{$cfg->bank_name} {$cfg->bank_account_number} a.n {$cfg->bank_account_holder}");
        $portalUrl = config('app.url', 'https://client.vexahostcloud.my.id');
        $brand = $cfg->brand_name ?: 'VexaHost';

        $previewSection = $project->link_website ? "🌐 Link Pratinjau Demo: {$project->link_website}\n\n" : "";

        return "Halo Kak {$nama}! ✨\n\n"
             . "Pengerjaan proyek *{$project->nama_project}* ({$project->paket_label}) saat ini telah berada pada tahap akhir (*Review Klien*) dan siap diluncurkan resmi (*LIVE*).\n\n"
             . $previewSection
             . "📌 *Rincian Tagihan Pelunasan Proyek:*\n"
             . "• Total Nilai Proyek: Rp {$total}\n"
             . "• Uang Muka (DP) Diterima: Rp {$terbayar}\n"
             . "• *Sisa Tagihan Pelunasan: Rp {$sisa}*\n\n"
             . "Sebelum tim kami melakukan peluncuran resmi (*Go-Live*) ke domain utama dan serah terima akses penuh sistem, mohon selesaikan pembayaran pelunasan ke rekening resmi kami:\n"
             . "🏦 *{$infoBank}*\n"
             . "📱 *QRIS:* Tersedia pada lembar invoice resmi\n\n"
             . "Kakak dapat memeriksa invoice dan mengunggah bukti transfer pelunasan melalui Client Panel:\n"
             . "🔗 {$portalUrl}\n\n"
             . "Atau cukup balas chat WhatsApp ini dengan menyertakan bukti transfer. Terima kasih banyak atas kerjasamanya bersama {$brand}! 🙏🚀";
    }

    /**
     * Template: Format Teks Pricelist & Paket Layanan.
     */
    public static function pricelist(?CompanySetting $settings = null): string
    {
        $cfg = self::resolveSettings($settings);
        $brand = $cfg->brand_name ?: 'VexaHost';
        $website = $cfg->website_url ?: 'https://vexahostcloud.my.id';
        $phone = $cfg->phone_support ?: '0858-0874-9131';

        return "Halo Kak! Terima kasih sudah menghubungi *{$brand}* 🙏\n\n"
             . "Berikut pilihan paket pembuatan website profesional & sistem digital kami:\n\n"
             . "🚀 *1. Landing Page UMKM / Sales Funnel — Rp 499.000*\n"
             . "• 1 Halaman Promosi / Sales Page Konversi Tinggi\n"
             . "• Desain Modern Responsif (Mobile & Desktop)\n"
             . "• Tombol Direct WhatsApp & Integrasi Google Maps\n"
             . "• Gratis Domain .my.id / .biz.id & Cloud Hosting 1 Tahun\n\n"
             . "💼 *2. Company Profile Bisnis — Rp 999.000*\n"
             . "• Hingga 5 Halaman Lengkap (Beranda, Profil, Layanan, Portofolio/Galeri, Kontak)\n"
             . "• Setup SEO Dasar & Pendaftaran Google Indexing\n"
             . "• Integrasi Form Kontak & Email Bisnis Berdomain Resmi\n"
             . "• Gratis Domain .com & Cloud Hosting 1 Tahun\n\n"
             . "🛒 *3. Toko Online E-Commerce & POS Kasir — Rp 1.500.000*\n"
             . "• Katalog Produk, Keranjang Belanja & Manajemen Pesanan\n"
             . "• Fitur Cetak Struk Bluetooth & Laporan Penjualan Otomatis\n"
             . "• Notifikasi WhatsApp Transaksi ke Pembeli & Admin\n"
             . "• Panduan & Pelatihan Penggunaan Lengkap\n\n"
             . "⚡ *4. Custom Web App / Sistem Informasi Khusus*\n"
             . "• Dashboard Admin, Manajemen Database, Multi-User Role, & API Integrasi\n"
             . "• Estimasi biaya disesuaikan dengan modul & kompleksitas sistem\n\n"
             . "🌐 Portofolio & Info: {$website}\n"
             . "💬 Konsultasi Langsung: {$phone}\n\n"
             . "Ada paket yang paling sesuai dengan target bisnis Kakak saat ini? Silakan ceritakan kebutuhan Anda ya! 😊";
    }

    /**
     * Template: Form Brief Desain & Kebutuhan Proyek.
     */
    public static function briefForm(?CompanySetting $settings = null): string
    {
        $cfg = self::resolveSettings($settings);
        $brand = strtoupper($cfg->brand_name ?: 'VEXAHOST');
        $phone = $cfg->phone_support ?: '0858-0874-9131';

        return "Halo Kak! Untuk memulai proses perancangan dan pengerjaan website, mohon bantu melengkapi data singkat berikut ya:\n\n"
             . "*FORM BRIEF KEBUTUHAN PROYEK — {$brand}*\n"
             . "1. Nama Usaha / Brand:\n"
             . "2. Bidang Usaha / Industri:\n"
             . "3. Paket Website / Sistem yang Dipilih:\n"
             . "4. Pilihan Nama Domain yang Diinginkan (cth: namabisnis.com):\n"
             . "5. Referensi Website yang Disukai (jika ada contoh/benchmark):\n"
             . "6. Pilihan Warna Dominan / Ciri Khas Brand:\n"
             . "7. Kontak Resmi (No. WhatsApp / Email / Alamat Bisnis):\n"
             . "8. Materi Logo & Foto Produk (bisa kirim via chat ini atau link Google Drive):\n\n"
             . "Jika ada pertanyaan atau butuh bantuan dalam pengisian brief, tim kami siap membantu via WhatsApp ({$phone}). Terima kasih banyak! 🙏";
    }

    /**
     * Template: Info Rekening Pembayaran Resmi & Ketentuan DP (100% dari CompanySetting).
     */
    public static function paymentAccountInfo(?CompanySetting $settings = null): string
    {
        $cfg = self::resolveSettings($settings);
        $brand = $cfg->brand_name ?: 'VexaHost';
        $company = $cfg->company_name ?: 'PT DESTINARA CHAKRAWALA ARTHA';
        $bank = $cfg->bank_name ?: '-';
        $accNo = $cfg->bank_account_number ?: '-';
        $accHolder = $cfg->bank_account_holder ?: '-';
        $phone = $cfg->phone_support ?: '0858-0874-9131';

        return "Halo Kak, untuk pembayaran pengerjaan proyek di *{$brand}* ({$company}), pembayaran resmi hanya sah apabila ditransfer melalui rekening atau QRIS resmi kami berikut:\n\n"
             . "🏦 *Bank Tujuan:* {$bank}\n"
             . "💳 *Nomor Rekening:* {$accNo}\n"
             . "👤 *Atas Nama:* {$accHolder}\n\n"
             . "📱 *Pembayaran QRIS:* Tersedia scan QRIS resmi pada dokumen invoice penagihan resmi.\n\n"
             . "📌 *Ketentuan Pembayaran:*\n"
             . "• Pembayaran Uang Muka (DP) minimal 50% untuk memulai tahap riset, setup server, dan pengerjaan kode.\n"
             . "• Pelunasan 50% diselesaikan setelah pengerjaan selesai direview dan sistem siap diluncurkan online (*LIVE*).\n"
             . "• Kwitansi resmi lunas bertanda tangan digital akan diterbitkan otomatis setelah dana terverifikasi.\n\n"
             . "Mohon konfirmasikan bukti transfer dengan mengirimkan struk ke nomor WhatsApp ini ({$phone}). Terima kasih banyak atas kepercayaannya! 🙏✨";
    }

    /**
     * Template: Format Teks Tagihan Pelunasan Siap Salin.
     */
    public static function settlementTemplate(?CompanySetting $settings = null): string
    {
        $cfg = self::resolveSettings($settings);
        $brand = $cfg->brand_name ?: 'VexaHost';
        $bank = $cfg->bank_name ?: '-';
        $accNo = $cfg->bank_account_number ?: '-';
        $accHolder = $cfg->bank_account_holder ?: '-';
        $phone = $cfg->phone_support ?: '0858-0874-9131';

        return "Halo Kak [Nama Klien]! ✨\n\n"
             . "Pemberitahuan bahwa pengerjaan proyek *[Nama Proyek]* saat ini telah selesai ditinjau (*Review Klien*) dan siap diluncurkan resmi (*LIVE*).\n\n"
             . "📌 *Rincian Tagihan Pelunasan:*\n"
             . "• Total Nilai Proyek: Rp [Total]\n"
             . "• Uang Muka (DP) Diterima: Rp [DP]\n"
             . "• *Sisa Tagihan Pelunasan: Rp [Sisa]*\n\n"
             . "Sebelum tim kami melakukan peluncuran (*Go-Live*) ke domain utama dan serah terima hak akses penuh sistem, mohon selesaikan pelunasan ke rekening resmi kami:\n\n"
             . "🏦 *{$bank}*\n"
             . "💳 *No. Rekening:* {$accNo}\n"
             . "👤 *Atas Nama:* {$accHolder}\n"
             . "📱 *QRIS:* Tersedia pada dokumen invoice tagihan pelunasan terlampir\n\n"
             . "Mohon kirimkan bukti transfer melalui chat WhatsApp ini ({$phone}). Terima kasih banyak atas kerjasamanya bersama {$brand}! 🙏🚀";
    }

    /**
     * Template: Format Penawaran Maintenance & Support.
     */
    public static function maintenanceOffer(?CompanySetting $settings = null): string
    {
        $cfg = self::resolveSettings($settings);
        $brand = $cfg->brand_name ?: 'VexaHost';
        $bank = $cfg->bank_name ?: '-';
        $accNo = $cfg->bank_account_number ?: '-';
        $accHolder = $cfg->bank_account_holder ?: '-';
        $phone = $cfg->phone_support ?: '0858-0874-9131';

        return "Halo Kak! Agar performa website tetap optimal, cepat, dan data bisnis selalu terlindungi, kami dari *{$brand}* menyediakan layanan pendampingan berkala:\n\n"
             . "🛡️ *Paket Pemeliharaan (Maintenance) & Cloud Guard (Mulai Rp 150.000 / Bulan)*\n"
             . "✅ Auto-Backup Database & Berkas Website Rutin ke Cloud Storage Aman\n"
             . "✅ Pemantauan Uptime Server 24/7 & Proteksi Keamanan (Anti-Malware & SSL Guard)\n"
             . "✅ Bantuan Update Teks, Foto Produk, Banner Promo & Penyesuaian Konten Tanpa Ribet Coding\n"
             . "✅ Konsultasi Teknis & Jalur Bantuan Prioritas via WhatsApp\n\n"
             . "💳 *Rekening Pembayaran Langganan:*\n"
             . "🏦 {$bank}\n"
             . "💳 No. Rekening: {$accNo} (a.n {$accHolder})\n\n"
             . "Dengan layanan pendampingan ini, Kakak bisa fokus penuh menjalankan omzet bisnis tanpa khawatir kendala teknis website. Cukup konfirmasi ke WhatsApp kami ({$phone}) jika ingin diaktifkan ya Kak. Terima kasih! 🙏✨";
    }

    /**
     * Template: Informasi Kontak Resmi & Saluran Bantuan.
     */
    public static function officialContactInfo(?CompanySetting $settings = null): string
    {
        $cfg = self::resolveSettings($settings);
        $brand = $cfg->brand_name ?: 'VexaHost';
        $company = $cfg->company_name ?: 'PT DESTINARA CHAKRAWALA ARTHA';
        $phone1 = $cfg->phone_support ?: '0858-0874-9131';
        $phone2 = $cfg->phone_support_2 ? "• WhatsApp Layanan 2: {$cfg->phone_support_2}\n" : '';
        $email = $cfg->email_support ?: 'vexahostcloudtech@gmail.com';
        $website = $cfg->website_url ?: 'https://vexahostcloud.my.id';
        $city = $cfg->domicile_city ?: 'Jakarta';

        return "Halo Kak! Berikut adalah saluran kontak dan informasi resmi *{$brand}* ({$company}):\n\n"
             . "📞 *Layanan WhatsApp & Konsultasi:*\n"
             . "• WhatsApp Layanan 1: {$phone1}\n"
             . $phone2
             . "✉️ *Email Resmi:* {$email}\n"
             . "🌐 *Website Resmi:* {$website}\n"
             . "📍 *Domisili Operasional:* {$city}\n\n"
             . "🕒 Jam Operasional Tim: Senin – Sabtu (09.00 – 21.00 WIB)\n"
             . "Ada kebutuhan website, sistem aplikasi, atau kendala teknis yang bisa kami bantu hari ini? Silakan sampaikan pesan Anda ya Kak. Terima kasih! 😊";
    }

    /**
     * Template: Pengingat Masa Berlaku Subscription Akan Habis (H-30 / H-7 / H-1).
     */
    public static function subscriptionExpiringReminder(Lead $lead, ProjectSubscription $subscription, ?CompanySetting $settings = null): string
    {
        $cfg = self::resolveSettings($settings);
        $nama = $lead->nama_kontak ?: $lead->nama_usaha;
        $project = $subscription->project;
        $sisaHari = $subscription->sisa_hari;
        $expiredDate = $subscription->tanggal_expired->translatedFormat('d F Y');
        $harga = number_format($subscription->harga, 0, ',', '.');
        $infoBank = $cfg->bank_info_string ?: "{$cfg->bank_name} {$cfg->bank_account_number} a.n {$cfg->bank_account_holder}";
        $brand = $cfg->brand_name ?: 'VexaHost';
        $phone = $cfg->phone_support ?: '0858-0874-9131';

        $urgency = match (true) {
            $sisaHari <= 1 => "⚠️ *BESOK* masa berlaku akan berakhir!",
            $sisaHari <= 7 => "⏳ Tinggal *{$sisaHari} hari lagi* masa berlaku akan berakhir.",
            default => "📅 Masa berlaku akan berakhir dalam *{$sisaHari} hari* lagi.",
        };

        return "Halo Kak {$nama}! 👋\n\n"
             . "Ini pengingat bahwa layanan *{$project->nama_project}* ({$subscription->tipe_label}) akan segera berakhir.\n\n"
             . "{$urgency}\n"
             . "📆 Tanggal Expired: *{$expiredDate}*\n\n"
             . "Agar layanan website/sistem Anda tetap aktif tanpa gangguan, silakan lakukan perpanjangan sebelum tanggal tersebut.\n\n"
             . "💰 *Biaya Perpanjangan: Rp {$harga}*\n"
             . "🏦 Transfer ke: {$infoBank}\n\n"
             . "Setelah transfer, mohon konfirmasi bukti pembayaran ke nomor ini ya Kak.\n\n"
             . "Jika ada pertanyaan atau ingin konsultasi paket perpanjangan, silakan hubungi tim {$brand} di {$phone}. Terima kasih! 🙏✨";
    }

    /**
     * Template: Notifikasi Subscription Sudah Expired.
     */
    public static function subscriptionExpired(Lead $lead, ProjectSubscription $subscription, ?CompanySetting $settings = null): string
    {
        $cfg = self::resolveSettings($settings);
        $nama = $lead->nama_kontak ?: $lead->nama_usaha;
        $project = $subscription->project;
        $expiredDate = $subscription->tanggal_expired->translatedFormat('d F Y');
        $harga = number_format($subscription->harga, 0, ',', '.');
        $infoBank = $cfg->bank_info_string ?: "{$cfg->bank_name} {$cfg->bank_account_number} a.n {$cfg->bank_account_holder}";
        $brand = $cfg->brand_name ?: 'VexaHost';
        $phone = $cfg->phone_support ?: '0858-0874-9131';

        return "Halo Kak {$nama},\n\n"
             . "Pemberitahuan bahwa masa berlaku layanan *{$project->nama_project}* ({$subscription->tipe_label}) telah *berakhir* pada tanggal *{$expiredDate}*.\n\n"
             . "⚠️ Layanan hosting, domain, dan dukungan teknis untuk sistem Anda saat ini *tidak aktif*. Website/aplikasi mungkin tidak dapat diakses oleh pelanggan Anda.\n\n"
             . "Untuk mengaktifkan kembali layanan, silakan lakukan pembayaran perpanjangan:\n"
             . "💰 *Biaya Perpanjangan: Rp {$harga}*\n"
             . "🏦 Transfer ke: {$infoBank}\n\n"
             . "Segera hubungi tim {$brand} di {$phone} untuk proses reaktivasi.\n\n"
             . "Terima kasih atas kerjasamanya! 🙏";
    }

    /**
     * Template: Konfirmasi Perpanjangan Subscription Berhasil.
     */
    public static function subscriptionRenewed(Lead $lead, ProjectSubscription $subscription, ?CompanySetting $settings = null): string
    {
        $cfg = self::resolveSettings($settings);
        $nama = $lead->nama_kontak ?: $lead->nama_usaha;
        $project = $subscription->project;
        $mulai = $subscription->tanggal_mulai->translatedFormat('d F Y');
        $expired = $subscription->tanggal_expired->translatedFormat('d F Y');
        $brand = $cfg->brand_name ?: 'VexaHost';

        return "Halo Kak {$nama}! 🎉\n\n"
             . "Perpanjangan layanan *{$project->nama_project}* ({$subscription->tipe_label}) telah berhasil diproses!\n\n"
             . "✅ *Masa Berlaku Baru:*\n"
             . "• Mulai: {$mulai}\n"
             . "• Berlaku Sampai: {$expired}\n\n"
             . "Layanan hosting, domain, dan dukungan teknis Anda kembali aktif sepenuhnya. 🚀\n\n"
             . "Terima kasih atas kepercayaannya terus bersama {$brand}! 🙏✨";
    }
}

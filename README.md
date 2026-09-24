# VexaHost Client (vexahost - buildclient)

Portal resmi **VexaHost** untuk klien & tim internal — gabungan dari dua aplikasi lama
(**CRM** dan **Portal Klien**) menjadi satu project dengan dua panel:

| Panel | URL | Untuk siapa | Isi |
|---|---|---|---|
| **Admin Panel** | `/admin/...` | admin, ceo, sales, project_manager, finance, technician | Leads & pipeline, proyek website, pembayaran/DP, invoice klien, maintenance, masa berlaku, riwayat WA, Kanban, tiket, pengguna & role, pengaturan perusahaan |
| **Client Panel** | `/client` + halaman proyek/tiket/tagihan | client | Dashboard, progres proyek (Kanban read-only), tiket bantuan, tagihan & upload bukti bayar, dokumen |

- Halaman utama `/` = **form login + landing page** (header, fitur, akses peran, footer).
- **Tidak ada pendaftaran mandiri.** Akun klien dibuat otomatis saat proyek dibuatkan akun di Admin Panel
  (tombol **Buat Akun Klien** di detail proyek / otomatis saat lead menjadi *Deal*), lalu info login dikirim via WhatsApp.
- Setelah login, pengguna otomatis diarahkan ke panel sesuai role-nya.

Produksi: **https://client.vexahostcloud.my.id** · Perusahaan: **PT DESTINARA CHAKRAWALA ARTHA**

---

## 🔗 Alur data (tanpa sinkronisasi HTTP lagi)

Semua berjalan di satu database lewat `App\Services\ProjectLifecycleService`:

1. **Proyek deal (CRM)** → akun klien + tugas Kanban + invoice klien.
2. **Pembayaran dicatat di CRM** → angka invoice klien otomatis diperbarui.
3. **Klien upload bukti bayar → admin verifikasi** → tercatat sebagai pembayaran CRM (DP / pelunasan).
4. **Kartu Kanban dipindah** → status proyek CRM ikut berubah + notifikasi WA ke klien.

WhatsApp dikirim lewat **VexaHost WA Gateway** (`wa.vexahostcloud.my.id`). URL & API key diatur di
**Admin Panel → Pengaturan Perusahaan** (bukan di `.env`).

---

## 🛠️ Stack

Laravel 12 · Tailwind CSS · Alpine.js · Vite · MySQL · Spatie Permission · DomPDF

---

## 📦 Menjalankan di Lokal (port **8054**)

```bash
composer install
npm install
cp .env.example .env        # lalu sesuaikan DB & ADMIN_PASSWORD
php artisan key:generate
php artisan migrate --seed
php artisan storage:link

npm run all                 # Laravel (http://localhost:8054) + Vite dev server
```

### Berbagi lewat ngrok

```bash
npm run all:ngrok           # build aset, lalu jalankan Laravel + ngrok ke port 8054
npm run ngrok               # hanya tunnel ngrok (jika server sudah jalan)
```

> Untuk ngrok gunakan `all:ngrok` (aset hasil build). Jika halaman tampil tanpa CSS, hapus file `public/hot`
> yang tertinggal dari `npm run dev`.

### Test

```bash
php artisan test
```

---

## ⏰ Scheduler

| Perintah | Jadwal | Fungsi |
|---|---|---|
| `crm:check-subscriptions` | 08:00 | Cek masa berlaku layanan & kirim pengingat |
| `crm:send-maintenance-reminders` | 09:00 | Pengingat tagihan maintenance H-3 |
| `ticket:check-sla` | tiap 15 menit | Notifikasi tiket yang melewati SLA |
| `notifications:prune`, `queue:prune-*` | harian | Membersihkan data lama |

Aktifkan dengan cron `* * * * * php artisan schedule:run` (lihat `PANDUAN_DEPLOY_COOLIFY.md`).

---

&copy; 2026 VexaHost · PT DESTINARA CHAKRAWALA ARTHA. All rights reserved.

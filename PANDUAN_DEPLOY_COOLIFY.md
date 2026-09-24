# 🚀 Panduan Deploy VexaHost Client ke Coolify

## 📌 Ringkasan

| Environment | Branch | Domain | Database |
|---|---|---|---|
| Production | `main` | `https://client.vexahostcloud.my.id` | `db_vexahost-client-production` |
| Staging | `staging` | `https://staging-client.vexahostcloud.my.id` | `db_vexahost-client-staging` |
| Development | lokal | `http://localhost:8054` | `db_vexahost-client-dev` |

---

## 📍 LANGKAH 1: DNS

Tambahkan record **A** `client` (dan `staging-client` bila perlu) yang mengarah ke IP server Coolify.

## 📍 LANGKAH 2: Database MySQL

Buat database MySQL di Coolify dengan nama sesuai tabel di atas, catat host internal, username & password-nya.

## 📍 LANGKAH 3: Resource Aplikasi

1. Coolify ➔ Project ➔ **`+ Add New Resource`** ➔ repository project ini.
2. **Build Pack**: Nixpacks · **Port**: `80` · **Domains**: URL HTTPS environment.
3. General Settings:

| Kolom | Nilai |
|---|---|
| Install Command | *(kosongkan — otomatis dari composer.json & package.json)* |
| Build Command | `npm run build` |
| Start Command | `php artisan serve --host=0.0.0.0 --port=80` |

## 📍 LANGKAH 4: Environment Variables

1. Salin isi `.env.production` (atau `.env.staging`) ke tab **Environment Variables**.
2. Isi nilai yang sengaja dikosongkan di file:
   - `APP_KEY` → hasil `php artisan key:generate --show`
   - `DB_HOST`, `DB_USERNAME`, `DB_PASSWORD`
   - `ADMIN_PASSWORD` (password akun admin pertama)
   - `MAIL_USERNAME`, `MAIL_PASSWORD` (Brevo SMTP)
3. API key WhatsApp **tidak perlu** di `.env` — isi setelah login di **Admin Panel ➔ Pengaturan Perusahaan**.

## 📍 LANGKAH 5: Persistent Storage

Tab **Persistent Storage** ➔ `+ Add`:
- **Name**: `vexahost-client-storage`
- **Mount Path**: `/app/storage/app/public`

(Menyimpan logo, QRIS, tanda tangan, bukti transfer & dokumen proyek.)

## 📍 LANGKAH 6: Post-Deployment Commands

```bash
php artisan optimize:clear && php artisan migrate --force && php artisan db:seed --force && php artisan storage:link --force && php artisan config:cache && php artisan route:cache && php artisan view:cache
```

`db:seed` aman diulang: hanya menyinkronkan permission, role bawaan, dan akun admin dari `.env`.

## 📍 LANGKAH 7: Scheduler & Queue

Tambahkan **Scheduled Task** di Coolify (setiap menit):

```bash
php artisan schedule:run
```

Karena `QUEUE_CONNECTION=database`, jalankan juga worker (resource worker / supervisor):

```bash
php artisan queue:work --sleep=3 --tries=3
```

## 📍 LANGKAH 8: Integrasi VexaHost WA Gateway

1. Login Admin Panel ➔ **Pengaturan Perusahaan** ➔ isi **WA Gateway API URL** & **API Key**, lalu **Uji Koneksi**.
2. Di dashboard `wa.vexahostcloud.my.id`, daftarkan webhook pesan masuk ke:
   `https://client.vexahostcloud.my.id/webhook/whatsapp`
3. Jika webhook memakai secret, isi `WA_WEBHOOK_SECRET` yang sama di environment (header `X-VexaHost-Signature`).

---

## ✅ Checklist Setelah Deploy

- [ ] `https://client.vexahostcloud.my.id` menampilkan form login + landing page
- [ ] Login admin masuk ke **Admin Panel** (`/admin/dashboard`)
- [ ] Pengaturan Perusahaan: rekening bank, QRIS, tanda tangan, nama direktur sudah diisi
- [ ] Uji kirim WA dari Pengaturan Perusahaan berhasil
- [ ] Buat lead ➔ Deal ➔ akun klien terbentuk & klien bisa login ke **Client Panel**
- [ ] Upload file bertahan setelah redeploy (persistent storage)

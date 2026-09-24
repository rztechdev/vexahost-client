<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Katalog izin (permission) sistem — Admin Panel (CRM) & Client Panel.
     *
     * Dikelompokkan agar mudah ditampilkan di Admin Panel > Role & Hak Akses.
     * Menambah fitur baru? Cukup tambahkan izinnya di sini lalu jalankan
     * `php artisan db:seed --class=RoleAndPermissionSeeder`.
     *
     * @var array<string, array<string, string>>
     */
    public const PERMISSIONS = [
        'Dashboard' => [
            'dashboard.view' => 'Melihat dashboard operasional',
            'crm.dashboard' => 'Melihat dashboard CRM (penjualan & keuangan)',
        ],
        'CRM & Penjualan' => [
            'leads.manage' => 'Kelola leads & pipeline',
            'crm.projects.manage' => 'Kelola proyek website (kontrak & status)',
            'messages.manage' => 'Kirim & kelola riwayat pesan WhatsApp',
        ],
        'Keuangan' => [
            'payments.manage' => 'Kelola pembayaran & DP',
            'maintenance.manage' => 'Kelola maintenance & masa berlaku layanan',
            'export.data' => 'Export data (CSV / PDF)',
        ],
        'Tiket' => [
            'tickets.create' => 'Membuat tiket (klien)',
            'tickets.view' => 'Melihat tiket',
            'tickets.handle' => 'Menangani tiket (teknisi)',
            'tickets.manage' => 'Kelola & tugaskan tiket (admin)',
        ],
        'Proyek Operasional' => [
            'projects.view' => 'Melihat proyek & papan Kanban',
            'projects.manage' => 'Kelola proyek operasional (buat/ubah/hapus)',
        ],
        'Tugas' => [
            'tasks.view' => 'Melihat tugas',
            'tasks.manage' => 'Kelola tugas (buat/ubah/hapus)',
            'tasks.update_progress' => 'Perbarui progres tugas (teknisi)',
        ],
        'Dokumen' => [
            'documents.manage' => 'Unggah & hapus dokumen',
        ],
        'Tagihan Klien' => [
            'invoices.view' => 'Melihat tagihan',
            'invoices.pay' => 'Konfirmasi pembayaran & upload bukti (klien)',
            'invoices.manage' => 'Kelola & verifikasi tagihan klien',
        ],
        'Administrasi' => [
            'users.manage' => 'Kelola pengguna',
            'roles.manage' => 'Kelola role & hak akses',
            'activity.view' => 'Melihat activity & audit log',
            'settings.manage' => 'Kelola pengaturan perusahaan & WA Gateway',
        ],
    ];

    /**
     * Preset role bawaan beserta izinnya.
     * Admin mendapatkan seluruh izin secara otomatis (lihat di bawah).
     *
     * @var array<string, array<int, string>>
     */
    public const ROLE_PRESETS = [
        'ceo' => [
            'dashboard.view', 'crm.dashboard', 'tickets.view', 'projects.view', 'tasks.view',
            'invoices.view', 'activity.view', 'export.data',
        ],
        'sales' => [
            'dashboard.view', 'crm.dashboard', 'leads.manage', 'crm.projects.manage',
            'messages.manage', 'projects.view', 'tasks.view', 'export.data',
        ],
        'project_manager' => [
            'dashboard.view', 'crm.dashboard', 'crm.projects.manage', 'messages.manage',
            'tickets.view', 'tickets.manage', 'projects.view', 'projects.manage',
            'tasks.view', 'tasks.manage', 'tasks.update_progress', 'documents.manage',
        ],
        'finance' => [
            'dashboard.view', 'crm.dashboard', 'crm.projects.manage', 'payments.manage',
            'maintenance.manage', 'messages.manage', 'invoices.view', 'invoices.manage',
            'projects.view', 'export.data',
        ],
        'technician' => [
            'dashboard.view', 'tickets.view', 'tickets.handle',
            'projects.view', 'tasks.view', 'tasks.update_progress', 'documents.manage',
        ],
        'client' => [
            'dashboard.view', 'tickets.create', 'tickets.view',
            'projects.view', 'tasks.view', 'invoices.view', 'invoices.pay',
        ],
    ];

    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Buat/sinkronkan seluruh izin dari katalog.
        $allPermissions = [];
        foreach (self::PERMISSIONS as $group) {
            foreach ($group as $name => $label) {
                Permission::firstOrCreate(['name' => $name]);
                $allPermissions[] = $name;
            }
        }

        // 2. Role admin: selalu memegang SEMUA izin (super admin).
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions($allPermissions);

        // 3. Preset role lain sebagai titik awal (bisa diubah admin lewat UI).
        foreach (self::ROLE_PRESETS as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);

            // Hanya isi izin default saat role pertama kali dibuat, agar
            // perubahan yang dilakukan admin lewat UI tidak tertimpa ulang.
            if ($role->wasRecentlyCreated) {
                $role->syncPermissions($permissions);
            }
        }

        // 4. Seed HANYA satu akun admin dari .env
        $admin = User::updateOrCreate(
            ['email' => config('portal.admin.email')],
            [
                'name' => config('portal.admin.name'),
                'password' => Hash::make(config('portal.admin.password')),
            ]
        );
        $admin->forceFill(['email_verified_at' => $admin->email_verified_at ?? now()])->save();

        if (! $admin->hasRole('admin')) {
            $admin->assignRole($adminRole);
        }
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan indeks performa secara aman (idempoten).
     * Jika indeks sudah dibuat pada percobaan migrasi sebelumnya atau sudah ada di MySQL,
     * migrasi tidak akan gagal karena Duplicate key name (Error 1061).
     */
    public function up(): void
    {
        // Tabel tickets
        $this->addIndexSafely('tickets', 'status');
        $this->addIndexSafely('tickets', 'client_id');
        $this->addIndexSafely('tickets', 'technician_id');

        // Tabel tasks
        $this->addIndexSafely('tasks', ['project_id', 'status']);
        $this->addIndexSafely('tasks', 'assignee_id');

        // Tabel invoices
        $this->addIndexSafely('invoices', ['client_id', 'status']);
        $this->addIndexSafely('invoices', 'project_id');

        // Tabel documents (sudah diindeks oleh $table->morphs() di migrasi awal, cek aman)
        $this->addIndexSafely('documents', ['documentable_type', 'documentable_id']);
    }

    public function down(): void
    {
        $this->dropIndexSafely('tickets', 'status');
        $this->dropIndexSafely('tickets', 'client_id');
        $this->dropIndexSafely('tickets', 'technician_id');

        $this->dropIndexSafely('tasks', ['project_id', 'status']);
        $this->dropIndexSafely('tasks', 'assignee_id');

        $this->dropIndexSafely('invoices', ['client_id', 'status']);
        $this->dropIndexSafely('invoices', 'project_id');

        // Catatan: Indeks documents tidak di-drop karena milik definisi $table->morphs() bawaan tabel
    }

    /**
     * Tambahkan index hanya bila belum ada, dan tangani galat duplicate key name (1061) MySQL.
     */
    private function addIndexSafely(string $table, string|array $columns, ?string $indexName = null): void
    {
        try {
            $name = $indexName ?? $this->buildIndexName($table, $columns);

            if (Schema::hasIndex($table, $name) || Schema::hasIndex($table, (array) $columns)) {
                return;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($columns, $indexName) {
                $blueprint->index($columns, $indexName);
            });
        } catch (\Throwable $e) {
            // Abaikan jika indeks sudah pernah dibuat (MySQL Error 1061: Duplicate key name)
            if (str_contains($e->getMessage(), '1061') || str_contains($e->getMessage(), 'Duplicate key')) {
                return;
            }
            throw $e;
        }
    }

    /**
     * Hapus index hanya bila ada, dan tangani galat jika tidak ditemukan (1091).
     */
    private function dropIndexSafely(string $table, string|array $columns, ?string $indexName = null): void
    {
        try {
            $name = $indexName ?? $this->buildIndexName($table, $columns);

            if (Schema::hasIndex($table, $name)) {
                Schema::table($table, function (Blueprint $blueprint) use ($name) {
                    $blueprint->dropIndex($name);
                });
            } elseif (Schema::hasIndex($table, (array) $columns)) {
                Schema::table($table, function (Blueprint $blueprint) use ($columns) {
                    $blueprint->dropIndex($columns);
                });
            }
        } catch (\Throwable $e) {
            // Abaikan jika indeks tidak ditemukan
            if (str_contains($e->getMessage(), '1091') || str_contains($e->getMessage(), "Can't DROP")) {
                return;
            }
            throw $e;
        }
    }

    private function buildIndexName(string $table, string|array $columns): string
    {
        if (is_array($columns)) {
            return strtolower($table . '_' . implode('_', $columns) . '_index');
        }
        return strtolower($table . '_' . $columns . '_index');
    }
};

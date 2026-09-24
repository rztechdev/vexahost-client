<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel inti gabungan Admin Panel (CRM) & Client Panel (Portal).
 *
 * Satu tabel `projects` dipakai bersama: kolom CRM (lead, paket, harga, status)
 * dan kolom operasional klien (client, manager, tiket, kanban).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('nama_usaha');
            $table->string('nama_kontak')->nullable();
            $table->string('kontak_wa');
            $table->string('email')->nullable();
            $table->enum('sumber', [
                'warm_network', 'cold_outreach', 'komunitas', 'marketplace', 'referral', 'website', 'lainnya',
            ])->default('warm_network');
            $table->enum('status', [
                'belum_dihubungi', 'sudah_chat', 'nego', 'deal', 'tidak_lanjut',
            ])->default('belum_dihubungi');
            $table->enum('paket_diminati', [
                'landing_page', 'company_profile', 'toko_kasir', 'custom', 'belum_tahu',
            ])->default('belum_tahu');
            $table->unsignedBigInteger('nilai_nego')->nullable();
            $table->text('catatan')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('follow_up_date');
            $table->index('kontak_wa');
        });

        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('status', ['open', 'pending', 'resolved', 'closed'])->default('open');
            $table->timestamp('sla_response_due_at')->nullable();
            $table->timestamp('sla_resolution_due_at')->nullable();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('first_response_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->boolean('sla_response_notified')->default(false);
            $table->boolean('sla_resolution_notified')->default(false);
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->nullable()->constrained('leads')->cascadeOnDelete();
            $table->foreignId('ticket_id')->nullable()->constrained('tickets')->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('paket')->default('landing_page');
            $table->unsignedBigInteger('harga')->default(0);
            // Status siklus hidup: draft → dp_diterima → dikerjakan → review → selesai / dibatalkan
            $table->enum('status', [
                'draft', 'dp_diterima', 'dikerjakan', 'review', 'selesai', 'dibatalkan',
            ])->default('draft');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('link_website')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamp('client_provisioned_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('lead_id');
            $table->index('client_id');
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->enum('jenis', ['dp', 'pelunasan', 'maintenance', 'lainnya'])->default('dp');
            $table->unsignedBigInteger('jumlah')->default(0);
            $table->enum('status', ['pending', 'lunas'])->default('pending');
            $table->date('tanggal');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index('project_id');
            $table->index('status');
        });

        Schema::create('maintenance_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->unsignedBigInteger('harga_bulanan')->default(150000);
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->date('tanggal_mulai');
            $table->date('tanggal_jatuh_tempo_berikutnya');
            $table->timestamp('terakhir_diingatkan_at')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index('lead_id');
            $table->index('status');
            $table->index('tanggal_jatuh_tempo_berikutnya');
        });

        Schema::create('project_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained('leads')->cascadeOnDelete();
            $table->enum('tipe', ['tahunan', 'bulanan', '6_bulan', 'custom'])->default('tahunan');
            $table->unsignedBigInteger('harga')->default(0);
            $table->date('tanggal_mulai');
            $table->date('tanggal_expired');
            $table->enum('status', ['aktif', 'akan_expired', 'expired', 'diperpanjang', 'nonaktif'])->default('aktif');
            $table->boolean('auto_renew')->default(false);
            $table->timestamp('terakhir_diingatkan_at')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index('project_id');
            $table->index('lead_id');
            $table->index('status');
            $table->index('tanggal_expired');
        });

        Schema::create('message_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->nullable()->constrained('leads')->cascadeOnDelete();
            $table->string('kontak_wa');
            $table->enum('arah', ['keluar', 'masuk'])->default('keluar');
            $table->string('tipe_pesan')->default('manual');
            $table->text('isi_pesan');
            $table->string('status_kirim')->default('sent');
            $table->json('response_payload')->nullable();
            $table->timestamps();

            $table->index('lead_id');
            $table->index('kontak_wa');
            $table->index('arah');
            $table->index('created_at');
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name')->nullable();
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('action');
            $table->text('description');
            $table->json('properties')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['subject_type', 'subject_id']);
            $table->index('action');
            $table->index('created_at');
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status')->default('todo');
            $table->string('priority')->default('medium');
            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('due_date')->nullable();
            $table->timestamps();
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->morphs('documentable');
            $table->string('file_path');
            $table->string('file_name');
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->string('invoice_number')->unique();
            $table->string('title');
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('balance_due', 15, 2)->default(0);
            $table->string('status')->default('unpaid');
            $table->date('due_date')->nullable();
            $table->string('payment_method')->nullable()->default('transfer_bank');
            $table->string('payment_type')->nullable()->default('full');
            $table->decimal('payment_amount_transferred', 15, 2)->nullable();
            $table->string('payment_proof')->nullable();
            $table->text('payment_notes')->nullable();
            $table->dateTime('payment_proof_uploaded_at')->nullable();
            $table->dateTime('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('message_logs');
        Schema::dropIfExists('project_subscriptions');
        Schema::dropIfExists('maintenance_subscriptions');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('leads');
    }
};

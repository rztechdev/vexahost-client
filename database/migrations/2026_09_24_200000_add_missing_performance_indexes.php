<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->index('status');
            $table->index('client_id');
            $table->index('technician_id');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->index(['project_id', 'status']);
            $table->index('assignee_id');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->index(['client_id', 'status']);
            $table->index('project_id');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->index(['documentable_type', 'documentable_id']);
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['client_id']);
            $table->dropIndex(['technician_id']);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['project_id', 'status']);
            $table->dropIndex(['assignee_id']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['client_id', 'status']);
            $table->dropIndex(['project_id']);
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex(['documentable_type', 'documentable_id']);
        });
    }
};

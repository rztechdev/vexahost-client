<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->default('PT DESTINARA CHAKRAWALA ARTHA');
            $table->string('brand_name')->default('VexaHost');
            $table->string('tagline')->default('Cloud Hosting & Jasa Pembuatan Website');
            $table->string('domicile_city')->default('Jakarta');
            $table->string('email_support')->default('vexahostcloudtech@gmail.com');
            $table->string('email_company')->default('vexahostcloudtech@gmail.com');
            $table->string('email_internal_alert')->default('vexahostcloudtech@gmail.com');
            $table->string('website_url')->default('https://vexahostcloud.my.id');
            $table->string('phone_support')->default('0858-0874-9131');
            $table->string('phone_support_2')->nullable();
            $table->string('phone_admin_alerts')->default('085808749131');
            $table->string('wa_api_url')->default('https://wa.vexahostcloud.my.id/api/v1/messages/text');
            $table->text('wa_api_key')->nullable();
            $table->string('wa_sender_phone')->nullable()->default('0858-0874-9131');
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_holder')->nullable();
            $table->string('qris_image_path')->nullable();
            $table->string('logo_image_path')->nullable()->default('images/logo.png');
            $table->string('signature_image_path')->nullable();
            $table->string('director_name')->nullable();
            $table->string('director_title')->nullable()->default('Direktur');
            $table->text('invoice_terms')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};

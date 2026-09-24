<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Admin Panel (CRM): cek masa berlaku layanan & pengingat otomatis
Schedule::command('crm:check-subscriptions')->dailyAt('08:00');

// Admin Panel (CRM): pengingat WhatsApp tagihan maintenance
Schedule::command('crm:send-maintenance-reminders')->dailyAt('09:00');

// Client Panel: pantau SLA tiket bantuan
Schedule::command('ticket:check-sla')->everyFifteenMinutes();

// Pembersihan database agar tetap ringan
Schedule::command('notifications:prune --days=30')->daily();
Schedule::command('queue:prune-failed --hours=168')->daily();
Schedule::command('queue:prune-batches --hours=168 --cancelled=72 --unfinished=168')->daily();

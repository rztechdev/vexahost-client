<?php

namespace App\Console\Commands;

use App\Models\MaintenanceSubscription;
use App\Services\WhatsApp\WhatsAppService;
use App\Services\WhatsApp\WhatsAppTemplates;
use Illuminate\Console\Command;

class SendMaintenanceReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:send-maintenance-reminders {--force : Kirim tanpa cek cooldown 20 hari}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim pesan pengingat WhatsApp otomatis untuk tagihan maintenance H-3 jatuh tempo';

    /**
     * Execute the console command.
     */
    public function handle(WhatsAppService $waService): int
    {
        $this->info('Memeriksa langganan maintenance yang mendekati jatuh tempo (H-3)...');

        $today = now()->startOfDay();
        $threeDaysLater = now()->addDays(3)->endOfDay();
        $force = $this->option('force');

        $subscriptions = MaintenanceSubscription::with('lead')
            ->where('status', 'aktif')
            ->whereBetween('tanggal_jatuh_tempo_berikutnya', [$today->toDateString(), $threeDaysLater->toDateString()])
            ->get();

        if ($subscriptions->isEmpty()) {
            $this->info('Tidak ada tagihan maintenance yang perlu diingatkan hari ini.');
            return Command::SUCCESS;
        }

        $sentCount = 0;
        $skippedCount = 0;

        foreach ($subscriptions as $sub) {
            $lead = $sub->lead;
            if (!$lead || empty($lead->kontak_wa)) {
                $this->warn("Skipped ID #{$sub->id}: Kontak WA tidak ditemukan.");
                $skippedCount++;
                continue;
            }

            // Check if already reminded in the last 20 days (to avoid multiple reminders in same month)
            if (!$force && $sub->terakhir_diingatkan_at && $sub->terakhir_diingatkan_at->diffInDays(now()) < 20) {
                $this->line("Skipped {$lead->nama_usaha}: Sudah diingatkan pada {$sub->terakhir_diingatkan_at->format('d/m/Y')}.");
                $skippedCount++;
                continue;
            }

            $message = WhatsAppTemplates::maintenanceReminder($lead, $sub);

            $this->line("Mengirim pengingat ke {$lead->nama_usaha} ({$lead->kontak_wa})...");

            $res = $waService->sendWhatsApp(
                to: $lead->kontak_wa,
                message: $message,
                lead: $lead,
                tipePesan: 'reminder_maintenance',
                isAutomated: true // Enforce deal status guardrail
            );

            if ($res['success'] ?? false) {
                $sub->update(['terakhir_diingatkan_at' => now()]);
                $this->info("✓ Sukses terkirim ke {$lead->nama_usaha}");
                $sentCount++;
            } else {
                $this->error("✗ Gagal kirim ke {$lead->nama_usaha}: " . ($res['message'] ?? 'Error API'));
            }
        }

        $this->info("Selesai! Terkirim: {$sentCount}, Dilewati: {$skippedCount}.");

        return Command::SUCCESS;
    }
}

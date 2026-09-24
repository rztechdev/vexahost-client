<?php

namespace App\Console\Commands;

use App\Models\ProjectSubscription;
use App\Services\ActivityLogger;
use App\Services\WhatsApp\WhatsAppService;
use App\Services\WhatsApp\WhatsAppTemplates;
use Illuminate\Console\Command;

class CheckExpiredSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:check-subscriptions {--force : Kirim reminder tanpa cek cooldown}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cek subscription project yang akan/sudah expired, kirim reminder WA, dan auto-nonaktifkan yang sudah lewat';

    /**
     * Execute the console command.
     */
    public function handle(WhatsAppService $waService): int
    {
        $this->info('Memeriksa status subscription project...');

        $force = $this->option('force');

        // 1. Auto-update status "akan_expired" untuk subscription H-30
        $akanExpired = ProjectSubscription::with(['project', 'lead'])
            ->whereIn('status', ['aktif', 'diperpanjang'])
            ->whereDate('tanggal_expired', '<=', now()->addDays(30)->toDateString())
            ->whereDate('tanggal_expired', '>', now()->toDateString())
            ->get();

        $statusUpdated = 0;
        foreach ($akanExpired as $sub) {
            if ($sub->status !== 'akan_expired') {
                $sub->update(['status' => 'akan_expired']);
                $statusUpdated++;
            }
        }

        if ($statusUpdated > 0) {
            $this->info("→ {$statusUpdated} subscription diubah ke status 'Akan Expired'.");
        }

        // 2. Auto-nonaktifkan subscription yang sudah expired
        $expired = ProjectSubscription::with(['project', 'lead'])
            ->whereIn('status', ['aktif', 'akan_expired', 'diperpanjang'])
            ->whereDate('tanggal_expired', '<', now()->toDateString())
            ->get();

        $expiredCount = 0;
        foreach ($expired as $sub) {
            $sub->update(['status' => 'expired']);
            $expiredCount++;

            ActivityLogger::log(
                'subscription_auto_expired',
                "Subscription project {$sub->project->nama_project} otomatis expired (tanggal: {$sub->tanggal_expired->format('d/m/Y')})",
                'ProjectSubscription',
                $sub->id
            );
        }

        if ($expiredCount > 0) {
            $this->warn("→ {$expiredCount} subscription otomatis di-expired-kan.");
        }

        // 3. Kirim WA reminder untuk H-30, H-7, H-1, dan yang sudah expired
        $needReminder = ProjectSubscription::with(['project', 'lead'])
            ->whereIn('status', ['aktif', 'akan_expired', 'diperpanjang', 'expired'])
            ->where(function ($query) {
                $query->whereDate('tanggal_expired', now()->addDays(30)->toDateString())
                    ->orWhereDate('tanggal_expired', now()->addDays(7)->toDateString())
                    ->orWhereDate('tanggal_expired', now()->addDays(1)->toDateString())
                    ->orWhereDate('tanggal_expired', now()->toDateString())
                    ->orWhere(function ($q) {
                        $q->where('status', 'expired')
                            ->whereDate('tanggal_expired', '>=', now()->subDays(3)->toDateString());
                    });
            })
            ->get();

        $sentCount = 0;
        $skippedCount = 0;

        foreach ($needReminder as $sub) {
            $lead = $sub->lead;
            if (!$lead || empty($lead->kontak_wa)) {
                $this->warn("Skipped ID #{$sub->id}: Kontak WA tidak ditemukan.");
                $skippedCount++;
                continue;
            }

            if (!$force && $sub->terakhir_diingatkan_at && $sub->terakhir_diingatkan_at->diffInDays(now()) < 1) {
                $this->line("Skipped {$lead->nama_usaha}: Sudah diingatkan hari ini.");
                $skippedCount++;
                continue;
            }

            $msg = $sub->isExpired()
                ? WhatsAppTemplates::subscriptionExpired($lead, $sub)
                : WhatsAppTemplates::subscriptionExpiringReminder($lead, $sub);

            $tipePesan = $sub->isExpired() ? 'subscription_expired' : 'subscription_reminder';

            $this->line("Mengirim reminder ke {$lead->nama_usaha} ({$lead->kontak_wa}) — sisa {$sub->sisa_hari} hari...");

            $res = $waService->sendWhatsApp(
                to: $lead->kontak_wa,
                message: $msg,
                lead: $lead,
                tipePesan: $tipePesan,
                isAutomated: true
            );

            if ($res['success'] ?? false) {
                $sub->update(['terakhir_diingatkan_at' => now()]);
                $this->info("✓ Sukses terkirim ke {$lead->nama_usaha}");
                $sentCount++;
            } else {
                $this->error("✗ Gagal kirim ke {$lead->nama_usaha}: " . ($res['message'] ?? 'Error API'));
            }
        }

        $this->info("Selesai! Status updated: {$statusUpdated}, Expired: {$expiredCount}, Reminder terkirim: {$sentCount}, Dilewati: {$skippedCount}.");

        return Command::SUCCESS;
    }
}

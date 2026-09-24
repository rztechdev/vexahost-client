<?php

namespace App\Services\WhatsApp;

use App\Models\Lead;
use App\Models\MessageLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $apiUrl;
    protected string $apiKey;

    /**
     * Check if currently running under test or PHPUnit environment.
     */
    public static function isTestEnvironment(): bool
    {
        return app()->environment('testing')
            || app()->runningUnitTests()
            || defined('PHPUNIT_COMPOSER_INSTALL')
            || defined('__PHPUNIT_PHAR__')
            || (isset($_SERVER['APP_ENV']) && $_SERVER['APP_ENV'] === 'testing')
            || (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'testing')
            || config('app.env') === 'testing';
    }

    public function __construct()
    {
        if (static::isTestEnvironment()) {
            $this->apiUrl = 'https://wa.vexahostcloud.my.id/api/v1/messages/text';
            $this->apiKey = '';
            return;
        }

        try {
            $settings = \App\Models\CompanySetting::get();
            $this->apiUrl = !empty($settings->wa_api_url) ? $settings->wa_api_url : config('whatsapp.api_url', 'https://wa.vexahostcloud.my.id/api/v1/messages/text');
            $this->apiKey = !empty($settings->wa_api_key) ? $settings->wa_api_key : config('whatsapp.api_key', '');
        } catch (\Throwable $e) {
            $this->apiUrl = config('whatsapp.api_url', 'https://wa.vexahostcloud.my.id/api/v1/messages/text');
            $this->apiKey = config('whatsapp.api_key', '');
        }
    }

    /**
     * Normalize WhatsApp phone number into standard international format or 08... format.
     */
    public function normalizePhoneNumber(string $phone): string
    {
        // Remove spaces, dashes, parentheses, non-numeric except leading +
        $cleaned = preg_replace('/[^\d+]/', '', $phone);

        if (str_starts_with($cleaned, '+')) {
            $cleaned = substr($cleaned, 1);
        }

        // Standardize 628... / 08... to standard numeric string
        if (str_starts_with($cleaned, '08')) {
            $cleaned = '628' . substr($cleaned, 2);
        } elseif (str_starts_with($cleaned, '8')) {
            $cleaned = '628' . substr($cleaned, 1);
        }

        return $cleaned;
    }

    /**
     * Send WhatsApp message via VexaHost WA Gateway.
     *
     * IMPORTANT ANTI-SPAM GUARDRAIL:
     * Auto-send ($isAutomated = true) is strictly FORBIDDEN for Leads with status
     * 'belum_dihubungi', 'sudah_chat', or 'nego'. Automated sending is ONLY allowed
     * for official clients (Deal status) on transactional moments:
     * 1. DP Received
     * 2. Project Completed
     * 3. Scheduled Maintenance Reminder (H-3)
     *
     * This protects the VexaHost WhatsApp number from being flagged/banned by Meta.
     */
    public function sendWhatsApp(
        string $to,
        string $message,
        ?Lead $lead = null,
        string $tipePesan = 'manual',
        bool $isAutomated = false
    ): array {
        $normalizedTo = $this->normalizePhoneNumber($to);

        // ==========================================
        // ANTI-SPAM GUARDRAIL CHECK
        // ==========================================
        if ($isAutomated && $lead) {
            $prohibitedStatuses = ['belum_dihubungi', 'sudah_chat', 'nego'];
            if (in_array($lead->status, $prohibitedStatuses)) {
                $errorMsg = "GUARDRAIL TRIGGERED: Automated WhatsApp message to non-deal lead (status: {$lead->status}) was blocked to protect sender number reputation.";
                Log::warning($errorMsg, [
                    'lead_id' => $lead->id,
                    'phone' => $normalizedTo,
                    'tipe_pesan' => $tipePesan,
                ]);

                // Create a failed log
                MessageLog::create([
                    'lead_id' => $lead->id,
                    'kontak_wa' => $normalizedTo,
                    'arah' => 'keluar',
                    'tipe_pesan' => $tipePesan,
                    'isi_pesan' => $message,
                    'status_kirim' => 'blocked_by_guardrail',
                    'response_payload' => ['error' => $errorMsg],
                ]);

                return [
                    'success' => false,
                    'message' => $errorMsg,
                    'status' => 'blocked_by_guardrail',
                ];
            }
        }

        // If in testing environment or API key is empty (local simulation or pending credentials)
        if (static::isTestEnvironment() || empty($this->apiKey)) {
            Log::info("VexaHost WA Gateway [SIMULATED]: Message sent to {$normalizedTo}", [
                'lead_id' => $lead?->id,
                'tipe_pesan' => $tipePesan,
                'message' => $message,
            ]);

            $log = MessageLog::create([
                'lead_id' => $lead?->id,
                'kontak_wa' => $normalizedTo,
                'arah' => 'keluar',
                'tipe_pesan' => $tipePesan,
                'isi_pesan' => $message,
                'status_kirim' => 'sent',
                'response_payload' => [
                    'simulated' => true,
                    'info' => 'WhatsApp message simulated locally (testing/development).',
                ],
            ]);

            return [
                'success' => true,
                'status' => 'simulated_sent',
                'data' => ['status' => 'queued'],
                'log_id' => $log->id,
            ];
        }

        try {
            $response = Http::withHeaders([
                'X-Api-Key' => $this->apiKey,
                'Accept' => 'application/json',
            ])->timeout(15)->post($this->apiUrl, [
                'to' => $normalizedTo,
                'message' => $message,
            ]);

            $responseBody = $response->json() ?? ['raw' => $response->body()];
            $isSuccess = $response->successful() && ($responseBody['success'] ?? true);

            $statusKirim = $isSuccess ? ($responseBody['data']['status'] ?? 'sent') : 'failed';

            $log = MessageLog::create([
                'lead_id' => $lead?->id,
                'kontak_wa' => $normalizedTo,
                'arah' => 'keluar',
                'tipe_pesan' => $tipePesan,
                'isi_pesan' => $message,
                'status_kirim' => $statusKirim,
                'response_payload' => $responseBody,
            ]);

            return [
                'success' => $isSuccess,
                'status' => $statusKirim,
                'data' => $responseBody,
                'log_id' => $log->id,
            ];
        } catch (\Throwable $e) {
            Log::error("VexaHost WA Gateway Exception: " . $e->getMessage(), [
                'to' => $normalizedTo,
                'lead_id' => $lead?->id,
            ]);

            $log = MessageLog::create([
                'lead_id' => $lead?->id,
                'kontak_wa' => $normalizedTo,
                'arah' => 'keluar',
                'tipe_pesan' => $tipePesan,
                'isi_pesan' => $message,
                'status_kirim' => 'failed',
                'response_payload' => ['error' => $e->getMessage()],
            ]);

            return [
                'success' => false,
                'status' => 'failed',
                'message' => $e->getMessage(),
                'log_id' => $log->id,
            ];
        }
    }

    /**
     * Send Media / Document (e.g. PDF Invoice, Kwitansi) via VexaHost WA Gateway.
     */
    public function sendMediaWhatsApp(
        string $to,
        string $fileContent,
        string $filename,
        string $caption = '',
        ?Lead $lead = null,
        string $tipePesan = 'invoice_pdf',
        string $mediaType = 'document'
    ): array {
        $normalizedTo = $this->normalizePhoneNumber($to);
        $mediaUrl = str_replace('/messages/text', '/messages/media', $this->apiUrl);
        if ($mediaUrl === $this->apiUrl) {
            $mediaUrl = 'https://wa.vexahostcloud.my.id/api/v1/messages/media';
        }

        // If in testing environment or API key is empty (local simulation)
        if (static::isTestEnvironment() || empty($this->apiKey)) {
            Log::info("VexaHost WA Media Gateway [SIMULATED]: Media {$filename} sent to {$normalizedTo}");

            $log = MessageLog::create([
                'lead_id' => $lead?->id,
                'kontak_wa' => $normalizedTo,
                'arah' => 'keluar',
                'tipe_pesan' => $tipePesan,
                'isi_pesan' => "[Lampiran Dokumen: {$filename}] " . $caption,
                'status_kirim' => 'sent',
                'response_payload' => [
                    'simulated' => true,
                    'filename' => $filename,
                    'info' => 'WhatsApp document simulated locally (testing/development).',
                ],
            ]);

            return [
                'success' => true,
                'status' => 'simulated_sent',
                'data' => ['status' => 'queued'],
                'log_id' => $log->id,
            ];
        }

        try {
            $response = Http::withHeaders([
                'X-Api-Key' => $this->apiKey,
                'Accept' => 'application/json',
            ])->timeout(30)->attach(
                'file',
                $fileContent,
                $filename
            )->post($mediaUrl, [
                'to' => $normalizedTo,
                'caption' => $caption,
                'type' => $mediaType,
            ]);

            $responseBody = $response->json() ?? ['raw' => $response->body()];
            $isSuccess = $response->successful() && ($responseBody['success'] ?? true);
            $statusKirim = $isSuccess ? ($responseBody['data']['status'] ?? 'sent') : 'failed';

            $log = MessageLog::create([
                'lead_id' => $lead?->id,
                'kontak_wa' => $normalizedTo,
                'arah' => 'keluar',
                'tipe_pesan' => $tipePesan,
                'isi_pesan' => "[Lampiran Dokumen: {$filename}] " . $caption,
                'status_kirim' => $statusKirim,
                'response_payload' => $responseBody,
            ]);

            return [
                'success' => $isSuccess,
                'status' => $statusKirim,
                'data' => $responseBody,
                'log_id' => $log->id,
            ];
        } catch (\Throwable $e) {
            Log::error("VexaHost WA Media Gateway Exception: " . $e->getMessage(), [
                'to' => $normalizedTo,
                'filename' => $filename,
                'lead_id' => $lead?->id,
            ]);

            $log = MessageLog::create([
                'lead_id' => $lead?->id,
                'kontak_wa' => $normalizedTo,
                'arah' => 'keluar',
                'tipe_pesan' => $tipePesan,
                'isi_pesan' => "[Lampiran Dokumen: {$filename}] " . $caption,
                'status_kirim' => 'failed',
                'response_payload' => ['error' => $e->getMessage()],
            ]);

            return [
                'success' => false,
                'status' => 'failed',
                'message' => $e->getMessage(),
                'log_id' => $log->id,
            ];
        }
    }
}

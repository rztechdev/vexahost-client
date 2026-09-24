<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\MessageLog;
use App\Services\WhatsApp\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    protected WhatsAppService $waService;

    public function __construct(WhatsAppService $waService)
    {
        $this->waService = $waService;
    }

    /**
     * Handle incoming webhook requests from VexaHost WA Gateway.
     */
    public function handle(Request $request)
    {
        $payload = $request->all();
        $rawContent = $request->getContent();

        Log::info('WA Gateway Webhook Received:', ['payload' => $payload]);

        // 1. Signature Verification (if WA_WEBHOOK_SECRET is set)
        $secret = config('whatsapp.webhook_secret');
        if (!empty($secret)) {
            $headerSignature = $request->header('X-VexaHost-Signature') ?? $request->header('X-Hub-Signature');
            if (!$headerSignature) {
                Log::warning('WA Gateway Webhook signature missing.');
                return response()->json(['error' => 'Missing signature'], 401);
            }

            $computedSignature = hash_hmac('sha256', $rawContent, $secret);
            if (!hash_equals($computedSignature, $headerSignature)) {
                Log::warning('WA Gateway Webhook invalid signature.', [
                    'received' => $headerSignature,
                    'computed' => $computedSignature,
                ]);
                return response()->json(['error' => 'Invalid signature'], 403);
            }
        }

        // 2. Extract sender phone and message text from various possible WA Gateway payload formats
        $from = $payload['from'] 
            ?? $payload['data']['from'] 
            ?? $payload['sender'] 
            ?? $payload['phone'] 
            ?? null;

        $messageText = $payload['message'] 
            ?? $payload['data']['message'] 
            ?? $payload['text'] 
            ?? $payload['data']['text'] 
            ?? $payload['body'] 
            ?? null;

        if (!$from || !$messageText) {
            // Might be status notification (e.g. delivered/read acknowledgment)
            if (isset($payload['event']) && in_array($payload['event'], ['message.status', 'ack'])) {
                return response()->json(['status' => 'status_event_received']);
            }

            return response()->json(['status' => 'ignored_no_message_data']);
        }

        // Normalize phone number
        $normalizedFrom = $this->waService->normalizePhoneNumber($from);

        // 3. Find matching Lead
        // Match exact, or match without country code
        $cleanShort = ltrim($normalizedFrom, '62');
        $lead = Lead::where(function ($query) use ($normalizedFrom, $from, $cleanShort) {
            $query->where('kontak_wa', $normalizedFrom)
                  ->orWhere('kontak_wa', $from)
                  ->orWhere('kontak_wa', 'like', "%{$cleanShort}");
        })->first();

        // 4. Save incoming message log
        $log = MessageLog::create([
            'lead_id' => $lead?->id,
            'kontak_wa' => $normalizedFrom,
            'arah' => 'masuk',
            'tipe_pesan' => 'webhook_masuk',
            'isi_pesan' => is_string($messageText) ? $messageText : json_encode($messageText),
            'status_kirim' => 'received',
            'response_payload' => $payload,
        ]);

        Log::info("WA Gateway Webhook: Incoming message saved from {$normalizedFrom}" . ($lead ? " (Lead: {$lead->nama_usaha})" : " (Unmatched Lead)"));

        return response()->json([
            'success' => true,
            'log_id' => $log->id,
            'lead_matched' => $lead ? true : false,
        ]);
    }
}

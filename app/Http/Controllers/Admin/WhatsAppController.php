<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Lead;
use App\Models\MessageLog;
use App\Services\WhatsApp\WhatsAppService;
use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    protected WhatsAppService $waService;

    public function __construct(WhatsAppService $waService)
    {
        $this->waService = $waService;
    }

    /**
     * Display all WhatsApp message logs across the CRM.
     */
    public function index(Request $request)
    {
        $query = MessageLog::with('lead');

        if ($request->filled('arah')) {
            $query->where('arah', $request->arah);
        }

        if ($request->filled('tipe')) {
            $query->where('tipe_pesan', $request->tipe);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('isi_pesan', 'like', "%{$q}%")
                    ->orWhere('kontak_wa', 'like', "%{$q}%")
                    ->orWhereHas('lead', function ($leadSub) use ($q) {
                        $leadSub->where('nama_usaha', 'like', "%{$q}%");
                    });
            });
        }

        $messages = $query->latest()->paginate(25)->withQueryString();

        return view('admin.messages.index', compact('messages'));
    }

    /**
     * Send manual WhatsApp message from CRM UI (Lead Detail).
     */
    public function sendManual(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'isi_pesan' => 'required|string|max:2000',
        ]);

        $lead = Lead::findOrFail($request->lead_id);

        $res = $this->waService->sendWhatsApp(
            to: $lead->kontak_wa,
            message: $request->isi_pesan,
            lead: $lead,
            tipePesan: 'manual',
            isAutomated: false // Manual user action from CRM interface
        );

        if ($res['success'] ?? false) {
            return back()->with('success', "Pesan WhatsApp berhasil dikirim ke {$lead->nama_usaha}.");
        }

        return back()->with('error', "Gagal mengirim pesan: " . ($res['message'] ?? 'Periksa log atau kredensial API.'));
    }

    /**
     * Remove a single message log entry.
     */
    public function destroy(MessageLog $messageLog)
    {
        $messageLog->delete();

        return back()->with('success', 'Riwayat pesan berhasil dihapus.');
    }

    /**
     * Remove all message log entries (bulk cleanup).
     */
    public function destroyAll()
    {
        $count = MessageLog::count();
        MessageLog::truncate();

        return redirect()->route('admin.messages.index')->with('success', "Seluruh {$count} riwayat pesan berhasil dihapus.");
    }
}

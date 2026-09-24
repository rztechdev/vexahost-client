<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\MaintenanceSubscription;
use App\Models\Lead;
use App\Models\Project;
use App\Services\ActivityLogger;
use App\Services\WhatsApp\WhatsAppService;
use App\Services\WhatsApp\WhatsAppTemplates;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    protected WhatsAppService $waService;

    public function __construct(WhatsAppService $waService)
    {
        $this->waService = $waService;
    }

    /**
     * Display a listing of maintenance subscriptions.
     */
    public function index(Request $request)
    {
        $query = MaintenanceSubscription::with(['lead', 'project']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $subscriptions = $query->orderBy('tanggal_jatuh_tempo_berikutnya', 'asc')->paginate(15)->withQueryString();

        $activeCount = MaintenanceSubscription::where('status', 'aktif')->count();
        $totalMRR = MaintenanceSubscription::where('status', 'aktif')->sum('harga_bulanan');

        // Check subscriptions that need reminder (within H-3)
        $needReminderCount = MaintenanceSubscription::where('status', 'aktif')
            ->where('tanggal_jatuh_tempo_berikutnya', '<=', now()->addDays(3)->toDateString())
            ->count();

        // Projects available for manual maintenance deal creation
        $availableProjects = Project::with('lead')
            ->where('status', '!=', 'dibatalkan')
            ->orderBy('name')
            ->get();

        $availableLeads = Lead::where('status', 'deal')
            ->orderBy('nama_usaha')
            ->get();

        return view('admin.maintenance.index', compact(
            'subscriptions',
            'activeCount',
            'totalMRR',
            'needReminderCount',
            'availableProjects',
            'availableLeads'
        ));
    }

    /**
     * Store a new maintenance subscription.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'project_id' => 'nullable|exists:projects,id',
            'harga_bulanan' => 'required|numeric|min:0',
            'status' => 'required|in:aktif,nonaktif',
            'tanggal_mulai' => 'required|date',
            'tanggal_jatuh_tempo_berikutnya' => 'required|date',
            'catatan' => 'nullable|string',
        ]);

        $sub = MaintenanceSubscription::create($validated);

        ActivityLogger::log('maintenance_created', "Mendaftarkan langganan maintenance untuk {$sub->lead->nama_usaha} (Rp " . number_format($sub->harga_bulanan, 0, ',', '.') . "/bln)", 'MaintenanceSubscription', $sub->id);

        return back()->with('success', "Langganan maintenance untuk {$sub->lead->nama_usaha} berhasil ditambahkan.");
    }

    /**
     * Remove the specified maintenance subscription.
     */
    public function destroy(MaintenanceSubscription $subscription)
    {
        $namaUsaha = $subscription->lead?->nama_usaha ?? 'Klien';
        $subId = $subscription->id;
        $subscription->delete();

        ActivityLogger::log('maintenance_deleted', "Menghapus langganan maintenance untuk {$namaUsaha}", 'MaintenanceSubscription', $subId);

        return back()->with('success', "Langganan maintenance untuk {$namaUsaha} berhasil dihapus.");
    }

    /**
     * Toggle maintenance subscription status (aktif / nonaktif).
     */
    public function toggleStatus(MaintenanceSubscription $subscription)
    {
        $newStatus = $subscription->status === 'aktif' ? 'nonaktif' : 'aktif';
        $subscription->update(['status' => $newStatus]);

        ActivityLogger::log('maintenance_status_changed', "Mengubah status maintenance {$subscription->lead->nama_usaha} menjadi " . strtoupper($newStatus), 'MaintenanceSubscription', $subscription->id);

        return back()->with('success', "Status maintenance {$subscription->lead->nama_usaha} berhasil diubah menjadi: " . strtoupper($newStatus));
    }

    /**
     * Send manual WhatsApp payment reminder for this maintenance subscription.
     */
    public function sendReminder(MaintenanceSubscription $subscription)
    {
        $lead = $subscription->lead;
        $message = WhatsAppTemplates::maintenanceReminder($lead, $subscription);

        $res = $this->waService->sendWhatsApp(
            to: $lead->kontak_wa,
            message: $message,
            lead: $lead,
            tipePesan: 'reminder_maintenance',
            isAutomated: true // Enforce deal status guardrail
        );

        if ($res['success'] ?? false) {
            $subscription->update(['terakhir_diingatkan_at' => now()]);
            ActivityLogger::log('maintenance_reminder_sent', "Mengirim WhatsApp reminder jatuh tempo maintenance ke {$lead->nama_usaha}", 'MaintenanceSubscription', $subscription->id);

            return back()->with('success', "Pengingat tagihan maintenance berhasil dikirim ke {$lead->nama_usaha} ({$lead->kontak_wa}).");
        }

        return back()->with('error', "Gagal mengirim pengingat: " . ($res['message'] ?? 'Periksa koneksi/kredensial API.'));
    }
}

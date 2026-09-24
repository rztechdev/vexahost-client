<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\ProjectSubscription;
use App\Models\Project;
use App\Models\Lead;
use App\Services\ActivityLogger;
use App\Services\WhatsApp\WhatsAppService;
use App\Services\WhatsApp\WhatsAppTemplates;
use Illuminate\Http\Request;

class ProjectSubscriptionController extends Controller
{
    protected WhatsAppService $waService;

    public function __construct(WhatsAppService $waService)
    {
        $this->waService = $waService;
    }

    /**
     * Display a listing of project subscriptions.
     */
    public function index(Request $request)
    {
        $query = ProjectSubscription::with(['project', 'lead']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->whereHas('project', fn ($p) => $p->where('name', 'like', "%{$q}%"))
                    ->orWhereHas('lead', fn ($l) => $l->where('nama_usaha', 'like', "%{$q}%")->orWhere('nama_kontak', 'like', "%{$q}%"));
            });
        }

        $subscriptions = $query->orderByRaw("CASE status WHEN 'expired' THEN 1 WHEN 'akan_expired' THEN 2 WHEN 'aktif' THEN 3 WHEN 'diperpanjang' THEN 4 ELSE 5 END")
            ->orderBy('tanggal_expired', 'asc')
            ->paginate(15)
            ->withQueryString();

        $statusCounts = [
            'all' => ProjectSubscription::count(),
            'aktif' => ProjectSubscription::where('status', 'aktif')->count(),
            'akan_expired' => ProjectSubscription::where('status', 'akan_expired')->count(),
            'expired' => ProjectSubscription::where('status', 'expired')->count(),
            'diperpanjang' => ProjectSubscription::where('status', 'diperpanjang')->count(),
            'nonaktif' => ProjectSubscription::where('status', 'nonaktif')->count(),
        ];

        $availableProjects = Project::with('lead')
            ->where('status', '!=', 'dibatalkan')
            ->orderBy('name')
            ->get();

        return view('admin.subscriptions.index', compact('subscriptions', 'statusCounts', 'availableProjects'));
    }

    /**
     * Store a new project subscription.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'tipe' => 'required|in:tahunan,bulanan,6_bulan,custom',
            'harga' => 'required|numeric|min:0',
            'tanggal_mulai' => 'required|date',
            'tanggal_expired' => 'required|date|after:tanggal_mulai',
            'auto_renew' => 'nullable|boolean',
            'catatan' => 'nullable|string',
        ]);

        $project = Project::findOrFail($validated['project_id']);
        $validated['lead_id'] = $project->lead_id;
        $validated['status'] = 'aktif';
        $validated['auto_renew'] = $request->boolean('auto_renew', false);

        $subscription = ProjectSubscription::create($validated);

        ActivityLogger::log(
            'subscription_created',
            "Mendaftarkan subscription {$subscription->tipe_label} untuk project {$project->nama_project} (Rp " . number_format($subscription->harga, 0, ',', '.') . ")",
            'ProjectSubscription',
            $subscription->id
        );

        return back()->with('success', "Subscription untuk project {$project->nama_project} berhasil ditambahkan.");
    }

    /**
     * Update subscription details.
     */
    public function update(Request $request, ProjectSubscription $subscription)
    {
        $validated = $request->validate([
            'tipe' => 'required|in:tahunan,bulanan,6_bulan,custom',
            'harga' => 'required|numeric|min:0',
            'tanggal_mulai' => 'required|date',
            'tanggal_expired' => 'required|date|after:tanggal_mulai',
            'auto_renew' => 'nullable|boolean',
            'catatan' => 'nullable|string',
        ]);

        $validated['auto_renew'] = $request->boolean('auto_renew', false);

        $subscription->update($validated);

        ActivityLogger::log(
            'subscription_updated',
            "Memperbarui subscription untuk project {$subscription->project->nama_project}",
            'ProjectSubscription',
            $subscription->id
        );

        return back()->with('success', "Subscription berhasil diperbarui.");
    }

    /**
     * Renew / perpanjang subscription.
     */
    public function renew(Request $request, ProjectSubscription $subscription)
    {
        $request->validate([
            'tipe' => 'nullable|in:tahunan,bulanan,6_bulan,custom',
            'tanggal_expired' => 'nullable|date',
            'harga' => 'nullable|numeric|min:0',
            'send_wa' => 'nullable|boolean',
        ]);

        if ($request->filled('tanggal_expired')) {
            $startDate = $subscription->tanggal_expired->isPast() ? now() : $subscription->tanggal_expired;
            $subscription->update([
                'tipe' => $request->input('tipe', $subscription->tipe),
                'harga' => $request->input('harga', $subscription->harga),
                'tanggal_mulai' => $startDate->toDateString(),
                'tanggal_expired' => $request->tanggal_expired,
                'status' => 'diperpanjang',
                'terakhir_diingatkan_at' => null,
            ]);
        } else {
            $subscription->renew($request->input('tipe'));
            if ($request->filled('harga')) {
                $subscription->update(['harga' => $request->harga]);
            }
        }

        ActivityLogger::log(
            'subscription_renewed',
            "Memperpanjang subscription project {$subscription->project->nama_project} sampai " . $subscription->tanggal_expired->format('d/m/Y'),
            'ProjectSubscription',
            $subscription->id
        );

        $lead = $subscription->lead;
        if ($request->boolean('send_wa', true) && $lead && !empty($lead->kontak_wa)) {
            $msg = WhatsAppTemplates::subscriptionRenewed($lead, $subscription);
            $this->waService->sendWhatsApp(
                to: $lead->kontak_wa,
                message: $msg,
                lead: $lead,
                tipePesan: 'subscription_renewed',
                isAutomated: false
            );
        }

        return back()->with('success', "Subscription {$subscription->project->nama_project} berhasil diperpanjang sampai " . $subscription->tanggal_expired->format('d/m/Y') . ".");
    }

    /**
     * Toggle subscription status (aktif / nonaktif).
     */
    public function toggleStatus(ProjectSubscription $subscription)
    {
        $newStatus = in_array($subscription->status, ['aktif', 'akan_expired', 'diperpanjang']) ? 'nonaktif' : 'aktif';
        $subscription->update(['status' => $newStatus]);

        ActivityLogger::log(
            'subscription_status_changed',
            "Mengubah status subscription {$subscription->project->nama_project} menjadi " . strtoupper($newStatus),
            'ProjectSubscription',
            $subscription->id
        );

        return back()->with('success', "Status subscription berhasil diubah menjadi: " . strtoupper($newStatus));
    }

    /**
     * Send manual WhatsApp reminder for expiring subscription.
     */
    public function sendReminder(ProjectSubscription $subscription)
    {
        $lead = $subscription->lead;
        if (!$lead || empty($lead->kontak_wa)) {
            return back()->with('error', 'Nomor WhatsApp klien tidak ditemukan.');
        }

        $msg = $subscription->isExpired()
            ? WhatsAppTemplates::subscriptionExpired($lead, $subscription)
            : WhatsAppTemplates::subscriptionExpiringReminder($lead, $subscription);

        $res = $this->waService->sendWhatsApp(
            to: $lead->kontak_wa,
            message: $msg,
            lead: $lead,
            tipePesan: 'subscription_reminder',
            isAutomated: false
        );

        if ($res['success'] ?? false) {
            $subscription->update(['terakhir_diingatkan_at' => now()]);

            ActivityLogger::log(
                'subscription_reminder_sent',
                "Mengirim reminder subscription {$subscription->project->nama_project} ke {$lead->kontak_wa}",
                'ProjectSubscription',
                $subscription->id
            );

            return back()->with('success', "Pengingat perpanjangan berhasil dikirim ke {$lead->nama_usaha} ({$lead->kontak_wa}).");
        }

        return back()->with('error', "Gagal mengirim pengingat: " . ($res['message'] ?? 'Periksa koneksi/kredensial API.'));
    }

    /**
     * Remove the specified subscription.
     */
    public function destroy(ProjectSubscription $subscription)
    {
        $projectName = $subscription->project->nama_project ?? 'Project';
        $subId = $subscription->id;
        $subscription->delete();

        ActivityLogger::log(
            'subscription_deleted',
            "Menghapus subscription untuk project {$projectName}",
            'ProjectSubscription',
            $subId
        );

        return back()->with('success', "Subscription untuk {$projectName} berhasil dihapus.");
    }
}

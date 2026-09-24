<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Lead;
use App\Models\Project;
use App\Models\Payment;
use App\Models\MaintenanceSubscription;
use App\Models\ProjectSubscription;
use App\Models\MessageLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Staf tanpa akses CRM (mis. teknisi) diarahkan ke Dashboard Operasional
        if (! auth()->user()->can('crm.dashboard')) {
            return redirect()->route('admin.operations');
        }

        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // 1. Total leads breakdown per status
        $totalLeads = Lead::count();
        $leadsPerStatus = [
            'belum_dihubungi' => Lead::where('status', 'belum_dihubungi')->count(),
            'sudah_chat' => Lead::where('status', 'sudah_chat')->count(),
            'nego' => Lead::where('status', 'nego')->count(),
            'deal' => Lead::where('status', 'deal')->count(),
            'tidak_lanjut' => Lead::where('status', 'tidak_lanjut')->count(),
        ];

        // 2. Potensi Pipeline (Total harga estimasi dari lead berstatus Sudah Chat + Nego)
        $pipelineLeads = Lead::whereIn('status', ['sudah_chat', 'nego'])->get();
        $potensiPipeline = $pipelineLeads->sum(function ($lead) {
            return $lead->getDefaultPackagePrice();
        });

        // 3. Realisasi Pendapatan (Kas Masuk Lunas) & Nilai Deal Closing Bulan Ini
        // A. Total Uang Masuk / Pembayaran Lunas di bulan ini (DP, Pelunasan, dll)
        $pendapatanMasukBulanIni = Payment::whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->where('status', 'lunas')
            ->sum('jumlah');

        // B. Total Nilai Kontrak Deal Proyek yang dibuat bulan berjalan (tidak dibatalkan)
        $projectsBulanIni = Project::with('payments')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->where('status', '!=', 'dibatalkan')
            ->get();

        $nilaiDealBulanIni = $projectsBulanIni->sum('harga');
        $projectDealCount = $projectsBulanIni->count();

        // C. Sisa Tagihan / Piutang dari proyek bulan ini (klien masih DP / belum lunas)
        $piutangBelumLunas = $projectsBulanIni->sum(function ($p) {
            return $p->remaining_balance;
        });

        // Jumlah klien proyek bulan ini yang status pembayarannya masih DP
        $klienMasihDpCount = $projectsBulanIni->filter(function ($p) {
            return $p->payment_status === 'dp_diterima';
        })->count();

        // 4. MRR dari maintenance aktif (Sum hargaBulanan semua MaintenanceSubscription berstatus Aktif)
        $mrrMaintenance = MaintenanceSubscription::where('status', 'aktif')->sum('harga_bulanan');
        $activeMaintenanceCount = MaintenanceSubscription::where('status', 'aktif')->count();

        // 5. Follow-up Hari Ini & Overdue Follow-ups
        $overdueFollowUps = Lead::whereNotNull('follow_up_date')
            ->where('follow_up_date', '<', $now->toDateString())
            ->whereNotIn('status', ['deal', 'tidak_lanjut'])
            ->orderBy('follow_up_date', 'asc')
            ->take(5)
            ->get();

        $todayFollowUps = Lead::where('follow_up_date', $now->toDateString())
            ->whereNotIn('status', ['deal', 'tidak_lanjut'])
            ->orderBy('created_at', 'desc')
            ->get();

        // 6. Proyek yang sedang berjalan (Dikerjakan / Review)
        $activeProjects = Project::with(['lead', 'payments'])
            ->whereIn('status', ['dp_diterima', 'dikerjakan', 'review'])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        // 7. Pesan WhatsApp Masuk Terbaru (Balasan klien)
        $recentIncomingMessages = MessageLog::with('lead')
            ->where('arah', 'masuk')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // 8. Riwayat Aktivitas Terkini (Proyek & Pembayaran)
        $recentProjects = Project::with('lead')->latest()->take(6)->get();

        // 9. Subscription / Masa Berlaku yang akan atau sudah expired
        $expiringSubscriptions = collect();
        $expiredSubscriptionCount = 0;
        $akanExpiredSubscriptionCount = 0;
        try {
            $expiringSubscriptions = ProjectSubscription::with(['project', 'lead'])
                ->whereIn('status', ['akan_expired', 'expired'])
                ->orderBy('tanggal_expired', 'asc')
                ->take(5)
                ->get();

            $expiredSubscriptionCount = ProjectSubscription::where('status', 'expired')->count();
            $akanExpiredSubscriptionCount = ProjectSubscription::where('status', 'akan_expired')->count();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('ProjectSubscription query failed: ' . $e->getMessage());
        }

        $stats = [
            'total_leads' => $totalLeads,
            'leads_per_status' => $leadsPerStatus,
            'potensi_pipeline' => $potensiPipeline,
            'closing_bulan_ini' => $pendapatanMasukBulanIni,
            'pendapatan_masuk_bulan_ini' => $pendapatanMasukBulanIni,
            'nilai_deal_bulan_ini' => $nilaiDealBulanIni,
            'piutang_belum_lunas' => $piutangBelumLunas,
            'klien_masih_dp_count' => $klienMasihDpCount,
            'project_deal_count' => $projectDealCount,
            'mrr_maintenance' => $mrrMaintenance,
            'active_maintenance_count' => $activeMaintenanceCount,
            'overdue_count' => Lead::whereNotNull('follow_up_date')->where('follow_up_date', '<', $now->toDateString())->whereNotIn('status', ['deal', 'tidak_lanjut'])->count(),
            'today_count' => Lead::where('follow_up_date', $now->toDateString())->whereNotIn('status', ['deal', 'tidak_lanjut'])->count(),
            'expired_subscription_count' => $expiredSubscriptionCount,
            'akan_expired_subscription_count' => $akanExpiredSubscriptionCount,
        ];

        return view('admin.dashboard', compact(
            'stats',
            'overdueFollowUps',
            'todayFollowUps',
            'activeProjects',
            'recentIncomingMessages',
            'recentProjects',
            'expiringSubscriptions'
        ));
    }
}

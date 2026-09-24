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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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
        $todayDate = $now->toDateString();

        // Cache stats selama 5 menit — invalidate otomatis setelah expired
        $stats = Cache::remember('admin_dashboard_stats', 300, function () use ($startOfMonth, $endOfMonth, $todayDate) {

            // 1. Total leads breakdown per status — 1 query (bukan 6)
            $leadCounts = Lead::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status')
                ->all();

            $totalLeads = array_sum($leadCounts);
            $leadsPerStatus = [
                'belum_dihubungi' => (int) ($leadCounts['belum_dihubungi'] ?? 0),
                'sudah_chat' => (int) ($leadCounts['sudah_chat'] ?? 0),
                'nego' => (int) ($leadCounts['nego'] ?? 0),
                'deal' => (int) ($leadCounts['deal'] ?? 0),
                'tidak_lanjut' => (int) ($leadCounts['tidak_lanjut'] ?? 0),
            ];

            // 2. Potensi Pipeline — tetap perlu get() karena getDefaultPackagePrice() ada logic di model
            $pipelineLeads = Lead::whereIn('status', ['sudah_chat', 'nego'])->get(['id', 'paket_diminati', 'nilai_nego']);
            $potensiPipeline = $pipelineLeads->sum(function ($lead) {
                return $lead->getDefaultPackagePrice();
            });

            // 3. Realisasi Pendapatan
            $pendapatanMasukBulanIni = Payment::whereBetween('tanggal', [$startOfMonth, $endOfMonth])
                ->where('status', 'lunas')
                ->sum('jumlah');

            $projectsBulanIni = Project::with('payments')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->where('status', '!=', 'dibatalkan')
                ->get();

            $nilaiDealBulanIni = $projectsBulanIni->sum('harga');
            $projectDealCount = $projectsBulanIni->count();

            $piutangBelumLunas = $projectsBulanIni->sum(function ($p) {
                return $p->remaining_balance;
            });

            $klienMasihDpCount = $projectsBulanIni->filter(function ($p) {
                return $p->payment_status === 'dp_diterima';
            })->count();

            // 4. MRR Maintenance — 1 query (bukan 2)
            $maintenanceStats = MaintenanceSubscription::where('status', 'aktif')
                ->selectRaw('SUM(harga_bulanan) as total_mrr, COUNT(*) as total_count')
                ->first();

            // 5. Follow-up counts — reuse dari data yang sudah ada, bukan query ulang
            $overdueCount = Lead::whereNotNull('follow_up_date')
                ->where('follow_up_date', '<', $todayDate)
                ->whereNotIn('status', ['deal', 'tidak_lanjut'])
                ->count();

            $todayCount = Lead::where('follow_up_date', $todayDate)
                ->whereNotIn('status', ['deal', 'tidak_lanjut'])
                ->count();

            // 6. Subscription counts — 1 query (bukan 2)
            $subCounts = ['expired' => 0, 'akan_expired' => 0];
            try {
                $subCounts = ProjectSubscription::whereIn('status', ['expired', 'akan_expired'])
                    ->select('status', DB::raw('count(*) as total'))
                    ->groupBy('status')
                    ->pluck('total', 'status')
                    ->all();
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('ProjectSubscription query failed: ' . $e->getMessage());
            }

            return [
                'total_leads' => $totalLeads,
                'leads_per_status' => $leadsPerStatus,
                'potensi_pipeline' => $potensiPipeline,
                'closing_bulan_ini' => $pendapatanMasukBulanIni,
                'pendapatan_masuk_bulan_ini' => $pendapatanMasukBulanIni,
                'nilai_deal_bulan_ini' => $nilaiDealBulanIni,
                'piutang_belum_lunas' => $piutangBelumLunas,
                'klien_masih_dp_count' => $klienMasihDpCount,
                'project_deal_count' => $projectDealCount,
                'mrr_maintenance' => (int) ($maintenanceStats->total_mrr ?? 0),
                'active_maintenance_count' => (int) ($maintenanceStats->total_count ?? 0),
                'overdue_count' => $overdueCount,
                'today_count' => $todayCount,
                'expired_subscription_count' => (int) ($subCounts['expired'] ?? 0),
                'akan_expired_subscription_count' => (int) ($subCounts['akan_expired'] ?? 0),
            ];
        });

        // Data non-cacheable (perlu fresh setiap load)
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

        $activeProjects = Project::with(['lead', 'payments'])
            ->whereIn('status', ['dp_diterima', 'dikerjakan', 'review'])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        $recentIncomingMessages = MessageLog::with('lead')
            ->where('arah', 'masuk')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentProjects = Project::with('lead')->latest()->take(6)->get();

        $expiringSubscriptions = collect();
        try {
            $expiringSubscriptions = ProjectSubscription::with(['project', 'lead'])
                ->whereIn('status', ['akan_expired', 'expired'])
                ->orderBy('tanggal_expired', 'asc')
                ->take(5)
                ->get();
        } catch (\Throwable $e) {
            // Already logged in cache closure
        }

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

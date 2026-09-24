<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private const TICKET_STATUSES = ['open', 'pending', 'resolved', 'closed'];

    private const TASK_STATUSES = ['todo', 'in_progress', 'review', 'done'];

    private const PROJECT_STATUSES = ['pending', 'active', 'completed', 'archived'];

    private const TICKET_PRIORITIES = ['low', 'medium', 'high'];

    public function index()
    {
        $data = $this->buildDashboardData(auth()->user());

        return view('dashboard', $data);
    }

    public function getKpiData(Request $request)
    {
        $user = auth()->user();

        return response()->json([
            'tickets' => $this->countsByField(
                $this->scopedTicketsQuery($user),
                'status',
                self::TICKET_STATUSES
            ),
            'tasks' => $this->countsByField(
                $this->scopedTasksQuery($user),
                'status',
                self::TASK_STATUSES
            ),
            'projects' => $this->projectWorkStatusCounts($this->scopedProjectsQuery($user)),
        ]);
    }

    private function buildDashboardData(User $user): array
    {
        $ticketsQuery = $this->scopedTicketsQuery($user);
        $tasksQuery = $this->scopedTasksQuery($user);
        $projectsQuery = $this->scopedProjectsQuery($user);

        $ticketsByStatus = $this->countsByField($ticketsQuery, 'status', self::TICKET_STATUSES);
        $tasksByStatus = $this->countsByField($tasksQuery, 'status', self::TASK_STATUSES);
        $projectsByStatus = $this->projectWorkStatusCounts($projectsQuery);
        $ticketsByPriority = $this->countsByField($ticketsQuery, 'priority', self::TICKET_PRIORITIES);

        $openTickets = (int) $ticketsQuery->clone()
            ->whereIn('status', ['open', 'pending'])
            ->count();

        $activeProjects = (int) $projectsQuery->clone()
            ->whereWorkStatus('active')
            ->count();

        $slaStats = $this->computeSlaStats($ticketsQuery->clone()->get());
        $projectsTrend = $this->monthOverMonthTrend($projectsQuery, 'created_at');

        return [
            'stats' => [
                'active_projects' => $activeProjects,
                'open_tickets' => $openTickets,
                'total_tickets' => (int) $ticketsQuery->clone()->count(),
                'sla_compliance_percent' => $slaStats['compliance_percent'],
                'sla_warning_count' => $slaStats['warning_count'],
                'sla_tracked_count' => $slaStats['tracked_count'],
                'projects_trend' => $projectsTrend,
            ],
            'charts' => [
                'tickets_by_status' => $this->chartPayload($ticketsByStatus, self::TICKET_STATUSES, 'statusTicket'),
                'tasks_by_status' => $this->chartPayload($tasksByStatus, self::TASK_STATUSES, 'statusTask'),
                'projects_by_status' => $this->barChartPayload($projectsByStatus, self::PROJECT_STATUSES, 'statusProject'),
                'tickets_by_priority' => $this->chartPayload($ticketsByPriority, self::TICKET_PRIORITIES, 'priority'),
                'tickets_monthly' => $this->ticketsMonthlyChart($ticketsQuery),
            ],
            'recent_activity' => $this->recentActivity($user),
            'invoice_summary' => [
                'total_due' => (float) ($user->hasRole('client') ? \App\Models\Invoice::where('client_id', $user->id)->where('status', '!=', 'paid')->sum('balance_due') : \App\Models\Invoice::where('status', '!=', 'paid')->sum('balance_due')),
                'unpaid_count' => (int) ($user->hasRole('client') ? \App\Models\Invoice::where('client_id', $user->id)->where('status', '!=', 'paid')->count() : \App\Models\Invoice::where('status', '!=', 'paid')->count()),
                'latest' => ($user->hasRole('client') ? \App\Models\Invoice::where('client_id', $user->id)->with('project')->latest()->take(2)->get() : \App\Models\Invoice::with(['project', 'client'])->latest()->take(2)->get()),
            ],
            'expiring_subscriptions' => $this->getExpiringSubscriptions($user),
        ];
    }

    private function scopedTicketsQuery(User $user)
    {
        $query = Ticket::query();

        if ($user->hasRole('client')) {
            $query->where('client_id', $user->id);
        } elseif ($user->hasRole('technician')) {
            $query->where('technician_id', $user->id);
        }

        return $query;
    }

    private function scopedTasksQuery(User $user)
    {
        $query = Task::query();

        if ($user->hasRole('client')) {
            $query->whereHas('project', fn ($q) => $q->where('client_id', $user->id));
        } elseif ($user->hasRole('technician')) {
            $query->where('assignee_id', $user->id);
        }

        return $query;
    }

    private function scopedProjectsQuery(User $user)
    {
        $query = Project::query();

        if ($user->hasRole('client')) {
            $query->where('client_id', $user->id);
        } elseif ($user->hasRole('technician')) {
            $query->where(fn ($q) => $q->where('manager_id', $user->id)
                ->orWhereHas('tasks', fn ($t) => $t->where('assignee_id', $user->id)));
        }

        return $query;
    }

    private function projectWorkStatusCounts($query): array
    {
        $counts = array_fill_keys(self::PROJECT_STATUSES, 0);
        foreach ((clone $query)->pluck('status') as $status) {
            $counts[Project::workStatusFor($status)]++;
        }

        return $counts;
    }

    private function countsByField($query, string $field, array $keys): array
    {
        $counts = (clone $query)
            ->select($field, DB::raw('count(*) as total'))
            ->groupBy($field)
            ->pluck('total', $field)
            ->all();

        $result = [];
        foreach ($keys as $key) {
            $result[$key] = (int) ($counts[$key] ?? 0);
        }

        foreach ($counts as $key => $total) {
            if (! array_key_exists($key, $result)) {
                $result[$key] = (int) $total;
            }
        }

        return $result;
    }

    private function chartPayload(array $counts, array $order, string $labelPrefix): array
    {
        $labels = [];
        $data = [];
        $colors = [];

        $palette = ['#EA580C', '#FB923C', '#F59E0B', '#C2410C', '#EF4444', '#94A3B8'];

        $i = 0;
        foreach ($order as $key) {
            if (! array_key_exists($key, $counts)) {
                continue;
            }
            $labels[] = $this->translateLabel($labelPrefix, $key);
            $data[] = $counts[$key];
            $colors[] = $palette[$i % count($palette)];
            $i++;
        }

        foreach ($counts as $key => $value) {
            if (in_array($key, $order, true)) {
                continue;
            }
            $labels[] = $this->translateLabel($labelPrefix, $key);
            $data[] = $value;
            $colors[] = $palette[$i % count($palette)];
            $i++;
        }

        return compact('labels', 'data', 'colors');
    }

    private function barChartPayload(array $counts, array $order, string $labelPrefix): array
    {
        $payload = $this->chartPayload($counts, $order, $labelPrefix);
        $payload['colors'] = array_map(fn ($c) => $c.'CC', $payload['colors']);

        return $payload;
    }

    private function translateLabel(string $prefix, string $key): string
    {
        $maps = [
            'statusTicket' => [
                'open' => 'Terbuka',
                'pending' => 'Menunggu',
                'resolved' => 'Selesai',
                'closed' => 'Ditutup',
            ],
            'statusTask' => [
                'todo' => 'Belum Mulai',
                'in_progress' => 'Berjalan',
                'review' => 'Review',
                'done' => 'Selesai',
            ],
            'statusProject' => [
                'pending' => 'Pending',
                'active' => 'Aktif',
                'completed' => 'Selesai',
                'archived' => 'Arsip',
            ],
            'priority' => [
                'low' => 'Rendah',
                'medium' => 'Sedang',
                'high' => 'Tinggi',
            ],
        ];

        return $maps[$prefix][$key] ?? ucfirst(str_replace('_', ' ', $key));
    }

    private function computeSlaStats(Collection $tickets): array
    {
        $tracked = $tickets->filter(fn (Ticket $t) => $t->sla_resolution_due_at !== null);

        if ($tracked->isEmpty()) {
            return [
                'compliance_percent' => 100,
                'warning_count' => 0,
                'tracked_count' => 0,
            ];
        }

        $ok = 0;
        $warning = 0;

        foreach ($tracked as $ticket) {
            $status = $ticket->slaStatus();
            if ($status === 'ok') {
                $ok++;
            } elseif ($status === 'warning') {
                $warning++;
            }
        }

        $compliant = $ok + $warning;
        $percent = (int) round(($compliant / $tracked->count()) * 100);

        return [
            'compliance_percent' => min(100, max(0, $percent)),
            'warning_count' => $warning,
            'tracked_count' => $tracked->count(),
        ];
    }

    private function monthOverMonthTrend($query, string $dateColumn): array
    {
        $now = Carbon::now();
        $thisMonth = (int) (clone $query)
            ->whereBetween($dateColumn, [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])
            ->count();

        $lastMonth = (int) (clone $query)
            ->whereBetween($dateColumn, [
                $now->copy()->subMonth()->startOfMonth(),
                $now->copy()->subMonth()->endOfMonth(),
            ])
            ->count();

        $diff = $thisMonth - $lastMonth;

        return [
            'diff' => $diff,
            'label' => $diff >= 0
                ? '+'.$diff.' dari bulan lalu'
                : (string) $diff.' dari bulan lalu',
            'positive' => $diff >= 0,
        ];
    }

    private function ticketsMonthlyChart($ticketsQuery): array
    {
        $start = Carbon::now()->subMonths(5)->startOfMonth();

        $monthExpr = match (DB::connection()->getDriverName()) {
            'mysql' => "DATE_FORMAT(created_at, '%Y-%m')",
            'pgsql' => "TO_CHAR(created_at, 'YYYY-MM')",
            default => "strftime('%Y-%m', created_at)",
        };

        $raw = (clone $ticketsQuery)
            ->where('created_at', '>=', $start)
            ->select(
                DB::raw("{$monthExpr} as month"),
                DB::raw('count(*) as total')
            )
            ->groupBy('month')
            ->pluck('total', 'month');

        $labels = [];
        $data = [];

        for ($i = 0; $i < 6; $i++) {
            $month = $start->copy()->addMonths($i);
            $key = $month->format('Y-m');
            $labels[] = $month->translatedFormat('M Y');
            $data[] = (int) ($raw[$key] ?? 0);
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    private function recentActivity(User $user): array
    {
        $items = collect();

        $tickets = $this->scopedTicketsQuery($user)
            ->with(['client', 'technician'])
            ->latest('updated_at')
            ->limit(8)
            ->get();

        foreach ($tickets as $ticket) {
            $items->push([
                'type' => 'ticket',
                'title' => $ticket->title,
                'assignee' => $ticket->technician?->name ?? $ticket->client?->name ?? '—',
                'status' => $ticket->status,
                'status_label' => $this->translateLabel('statusTicket', $ticket->status),
                'updated_at' => $ticket->updated_at,
                'url' => null,
            ]);
        }

        $projects = $this->scopedProjectsQuery($user)
            ->with('manager')
            ->latest('updated_at')
            ->limit(5)
            ->get();

        foreach ($projects as $project) {
            $items->push([
                'type' => 'project',
                'title' => $project->name,
                'assignee' => $project->manager?->name ?? '—',
                'status' => $project->work_status,
                'status_label' => $this->translateLabel('statusProject', $project->work_status),
                'updated_at' => $project->updated_at,
                'url' => route('projects.show', $project),
            ]);
        }

        return $items
            ->sortByDesc('updated_at')
            ->take(10)
            ->values()
            ->map(fn ($item) => array_merge($item, [
                'time_ago' => $item['updated_at']?->diffForHumans() ?? '—',
            ]))
            ->all();
    }

    private function getExpiringSubscriptions(User $user): array
    {
        $query = $this->scopedProjectsQuery($user)
            ->whereHas('subscriptions', fn ($s) => $s->whereIn('status', ['akan_expired', 'expired']))
            ->with('activeSubscription');

        return $query->get()->map(fn (Project $p) => [
            'id'     => $p->id,
            'name'   => $p->name,
            'status' => $p->subscription_status,
            'label'  => $p->subscription_status_label,
            'color'  => $p->subscription_status_color,
            'expired' => $p->subscription_expired?->format('d M Y'),
            'sisa_hari' => $p->subscription_sisa_hari,
            'price'  => $p->subscription_price,
            'url'    => route('projects.show', $p),
        ])->all();
    }
}

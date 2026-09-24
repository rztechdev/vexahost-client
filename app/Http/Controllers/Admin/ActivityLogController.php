<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display audit trail / activity logs.
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('description', 'like', "%{$q}%")
                    ->orWhere('user_name', 'like', "%{$q}%")
                    ->orWhere('action', 'like', "%{$q}%");
            });
        }

        $logs = $query->latest()->paginate(25)->withQueryString();
        $users = User::orderBy('name')->get();

        $actionTypes = [
            'lead_created' => 'Lead Dibuat',
            'lead_updated' => 'Lead Diperbarui',
            'lead_converted' => 'Lead Deal & Convert',
            'lead_deleted' => 'Lead Dihapus',
            'project_created' => 'Project Dibuat',
            'project_status_changed' => 'Status Project Diubah',
            'project_updated' => 'Project Diperbarui',
            'payment_created' => 'Pembayaran Ditambahkan',
            'payment_status_changed' => 'Status Pembayaran Diubah',
            'maintenance_created' => 'Maintenance Dibuat',
            'maintenance_reminder_sent' => 'Reminder Maintenance Terkirim',
            'user_created' => 'User Dibuat',
            'user_updated' => 'User Diperbarui',
        ];

        return view('admin.activity_logs.index', compact('logs', 'users', 'actionTypes'));
    }

    /**
     * Remove a single activity log entry.
     */
    public function destroy(ActivityLog $activityLog)
    {
        $activityLog->delete();

        return back()->with('success', 'Log aktivitas berhasil dihapus.');
    }

    /**
     * Remove all activity log entries (bulk cleanup).
     */
    public function destroyAll()
    {
        $count = ActivityLog::count();
        ActivityLog::truncate();

        return redirect()->route('admin.activity-logs.index')->with('success', "Seluruh {$count} log aktivitas berhasil dihapus.");
    }
}

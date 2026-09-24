<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Services\TicketWorkflowService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function create(Request $request)
    {
        $project = Project::findOrFail($request->project_id);
        $users = User::role(['admin', 'project_manager', 'technician'])->get();
        return view('tasks.create', compact('project', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id'  => 'required|exists:projects,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'required|in:todo,in_progress,review,done',
            'priority'    => 'required|in:low,medium,high',
            'assignee_id' => 'nullable|exists:users,id',
            'due_date'    => 'nullable|date',
        ]);

        $task = Task::create($request->only(['project_id','name','description','status','priority','assignee_id','due_date']));

        if ($task->assignee_id) {
            app(TicketWorkflowService::class)->notifyTaskAssigned($task);
        }

        return redirect()->route('projects.show', $request->project_id)->with('success', 'Tugas berhasil ditambahkan.');
    }

    public function show(Task $task)
    {
        $user = auth()->user();
        if ($user->hasRole('ceo')) {
            // CEO: read-only access to all tasks
        } elseif ($user->hasRole('client') && $task->project->client_id !== $user->id) {
            abort(403, 'Anda tidak diizinkan mengakses tugas ini.');
        } elseif ($user->hasRole('technician') && $task->assignee_id !== $user->id && $task->project->manager_id !== $user->id) {
            abort(403, 'Anda tidak diizinkan mengakses tugas ini.');
        }

        $task->load(['project', 'assignee', 'documents.uploader']);
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $users = User::role(['admin', 'project_manager', 'technician'])->get();
        return view('tasks.edit', compact('task', 'users'));
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'required|in:todo,in_progress,review,done',
            'priority'    => 'required|in:low,medium,high',
            'assignee_id' => 'nullable|exists:users,id',
            'due_date'    => 'nullable|date',
        ]);

        $previousAssigneeId = $task->assignee_id;

        $task->update($request->only(['name','description','status','priority','assignee_id','due_date']));

        if ($task->assignee_id && $task->assignee_id !== $previousAssigneeId) {
            app(TicketWorkflowService::class)->notifyTaskAssigned($task->fresh(), $previousAssigneeId !== null);
        }

        return redirect()->route('projects.show', $task->project_id)->with('success', 'Tugas berhasil diperbarui.');
    }

    public function destroy(Task $task)
    {
        $projectId = $task->project_id;
        $task->delete();
        return redirect()->route('projects.show', $projectId)->with('success', 'Tugas berhasil dihapus.');
    }

    /**
     * Update task progress status (technician/admin action).
     * Clients are strictly read-only and denied.
     */
    public function updateProgress(Request $request, Task $task)
    {
        $user = auth()->user();

        // 1. Strict Read-Only protection for Client role
        if ($user->isClient() || $task->project->client_id === $user->id) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak: Klien hanya memiliki akses baca (read-only) pada papan kanban.',
                ], 403);
            }
            abort(403, 'Akses ditolak: Klien hanya memiliki akses baca (read-only) pada papan kanban.');
        }

        // 2. Only admin, technician, or project manager can update
        $canUpdate = $user->hasRole('admin')
            || $user->can('tasks.manage')
            || ($user->hasRole('technician') && ($task->assignee_id === $user->id || $task->project->manager_id === $user->id))
            || $task->project->manager_id === $user->id;

        if (!$canUpdate) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Anda tidak memiliki izin untuk memperbarui tugas ini.'], 403);
            }
            abort(403, 'Anda tidak memiliki izin untuk memperbarui status tugas ini.');
        }

        $request->validate([
            'status'       => 'required|in:todo,in_progress,review,done',
            'link_website' => 'nullable|string',
            'send_wa'      => 'nullable|boolean',
        ]);

        $task->update(['status' => $request->status]);

        // Terapkan ke status proyek (Admin Panel) & kirim notifikasi WA ke klien
        $sync = app(\App\Services\ProjectLifecycleService::class)->applyKanbanStatus(
            $task->fresh('project'),
            $request->input('link_website'),
            $request->boolean('send_wa', true)
        );

        $waNotice = '';
        if (! empty($sync['wa_sent'])) {
            $waNotice = ' & Notifikasi WhatsApp otomatis terkirim ke klien.';
        } elseif ($request->boolean('send_wa', true) && ! empty($sync['wa_error'])) {
            $waNotice = ' (Catatan: Pesan WA belum terkirim — ' . $sync['wa_error'] . ').';
        }
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'      => true,
                'message'      => 'Status tugas & proyek berhasil diperbarui ke ' . ucfirst(str_replace('_', ' ', $task->status)) . $waNotice,
                'task'         => $task,
                'crm_sync'     => $sync,
                'link_website' => $sync['link_website'],
            ]);
        }

        return back()->with('success', 'Progress tugas berhasil diperbarui.' . $waNotice);
    }
}

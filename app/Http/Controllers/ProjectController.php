<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Models\Ticket;
use App\Services\TicketWorkflowService;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isStaff() && ! $user->hasRole('technician')) {
            $projects = Project::with(['client', 'manager', 'tasks'])->latest()->paginate(10);
        } elseif ($user->hasRole('technician')) {
            $projects = Project::with(['client', 'manager', 'tasks'])
                ->where(fn ($q) => $q->where('manager_id', $user->id)
                    ->orWhereHas('tasks', fn ($t) => $t->where('assignee_id', $user->id)))
                ->latest()->paginate(10);
        } else {
            // client
            $projects = Project::with(['client', 'manager', 'tasks'])
                ->where('client_id', $user->id)
                ->latest()->paginate(10);
        }

        return view('projects.index', compact('projects'));
    }

    public function create(Request $request)
    {
        $clients = User::role('client')->get();
        $managers = User::role(['admin', 'project_manager', 'technician'])->get();
        $ticket = null;
        if ($request->has('ticket_id')) {
            $ticket = Ticket::find($request->ticket_id);
        }
        return view('projects.create', compact('clients', 'managers', 'ticket'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'description'=> 'nullable|string',
            'work_status' => 'required|in:pending,active,completed,archived',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'client_id'  => 'required|exists:users,id',
            'manager_id' => 'nullable|exists:users,id',
            'ticket_id'  => 'nullable|exists:tickets,id',
        ]);

        $project = Project::create($request->only(['name','description','work_status','start_date','end_date','client_id','manager_id','ticket_id']));

        $workflow = app(TicketWorkflowService::class);
        $workflow->markTicketInProgressFromProject($project);
        $workflow->syncTicketTechnicianFromProject($project->fresh());
        $project->syncLinkedTicketStatus();
        $workflow->notifyProjectCreated($project->fresh());

        return redirect()->route('projects.show', $project)->with('success', 'Proyek berhasil dibuat. Klien dan teknisi terkait telah diberi notifikasi.');
    }

    public function show(Project $project)
    {
        $user = auth()->user();
        if ($user->hasRole('ceo')) {
            // CEO: read-only access to all projects
        } elseif ($user->isClient() && $project->client_id !== $user->id) {
            abort(403, 'Anda tidak diizinkan mengakses proyek ini.');
        } elseif ($user->hasRole('technician') && $project->manager_id !== $user->id && !$project->tasks()->where('assignee_id', $user->id)->exists()) {
            abort(403, 'Anda tidak diizinkan mengakses proyek ini.');
        }

        if ($project->tasks()->count() === 0) {
            \App\Models\Task::create([
                'project_id'  => $project->id,
                'name'        => 'Pengerjaan ' . $project->name,
                'description' => 'Tugas utama pengerjaan website.',
                'status'      => app(\App\Services\ProjectLifecycleService::class)->kanbanStatusFor($project->status),
                'priority'    => 'medium',
                'assignee_id' => $project->manager_id,
            ]);
        }

        $project->load(['client', 'manager', 'tasks.assignee', 'documents.uploader', 'latestInvoice']);
        $users = User::role(['admin', 'project_manager', 'technician'])->get();
        return view('projects.show', compact('project', 'users'));
    }

    public function edit(Project $project)
    {
        $clients = User::role('client')->get();
        $managers = User::role(['admin', 'project_manager', 'technician'])->get();
        return view('projects.edit', compact('project', 'clients', 'managers'));
    }

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'description'=> 'nullable|string',
            'work_status' => 'required|in:pending,active,completed,archived',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'client_id'  => 'required|exists:users,id',
            'manager_id' => 'nullable|exists:users,id',
            'ticket_id'  => 'nullable|exists:tickets,id',
        ]);

        $previousManagerId = $project->manager_id;

        $project->update($request->only(['name','description','work_status','start_date','end_date','client_id','manager_id','ticket_id']));

        $workflow = app(TicketWorkflowService::class);
        if ($project->manager_id !== $previousManagerId) {
            $workflow->notifyManagerChanged($project->fresh(), $previousManagerId);
        } else {
            $workflow->syncTicketTechnicianFromProject($project->fresh());
        }

        return redirect()->route('projects.show', $project)->with('success', 'Proyek berhasil diperbarui.');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Proyek berhasil dihapus.');
    }
}

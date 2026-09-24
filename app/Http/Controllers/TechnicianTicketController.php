<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class TechnicianTicketController extends Controller
{
    /**
     * Display a listing of all tickets for technicians.
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'semua');
        $userId = Auth::id();
        $query = Ticket::with(['client', 'technician', 'project.manager']);

        if ($tab === 'belum_ditugaskan') {
            $query->whereNull('technician_id')->whereDoesntHave('project');
        } elseif ($tab === 'ditugaskan_ke_saya') {
            $query->where(function ($q) use ($userId) {
                $q->where('technician_id', $userId)
                    ->orWhereHas('project', fn ($p) => $p->where('manager_id', $userId))
                    ->orWhereHas('project.tasks', fn ($t) => $t->where('assignee_id', $userId));
            });
        }

        $tickets = $query->orderBy('created_at', 'desc')->get();

        return view('technician.tickets.index', compact('tickets', 'tab'));
    }
}

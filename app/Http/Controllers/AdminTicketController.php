<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class AdminTicketController extends Controller
{
    /**
     * Display a listing of all tickets for Admin.
     */
    public function index()
    {
        $tickets = Ticket::with(['client', 'technician', 'project'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.tickets.index', compact('tickets'));
    }
}

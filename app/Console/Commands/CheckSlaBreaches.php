<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketSlaBreachedNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class CheckSlaBreaches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ticket:check-sla';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for SLA breaches and notify technicians';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();

        // 1. Check Response SLA Breaches
        $responseBreaches = Ticket::where('status', 'open')
            ->whereNull('first_response_at')
            ->where('sla_response_notified', false)
            ->where('sla_response_due_at', '<', $now)
            ->get();

        $adminsAndTechnicians = User::role(['admin', 'technician'])->get();

        foreach ($responseBreaches as $ticket) {
            Notification::send($adminsAndTechnicians, new TicketSlaBreachedNotification($ticket, 'response'));
            $ticket->update(['sla_response_notified' => true]);
        }

        // 2. Check Resolution SLA Breaches
        $resolutionBreaches = Ticket::whereIn('status', ['open', 'pending'])
            ->whereNull('resolved_at')
            ->where('sla_resolution_notified', false)
            ->where('sla_resolution_due_at', '<', $now)
            ->get();

        foreach ($resolutionBreaches as $ticket) {
            // Notify assigned technician if any, else all technicians
            $notifiables = $ticket->technician ? collect([$ticket->technician]) : $adminsAndTechnicians;
            Notification::send($notifiables, new TicketSlaBreachedNotification($ticket, 'resolution'));
            $ticket->update(['sla_resolution_notified' => true]);
        }

        $this->info("Checked SLA breaches: " . $responseBreaches->count() . " response breaches, " . $resolutionBreaches->count() . " resolution breaches.");
    }
}

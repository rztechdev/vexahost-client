<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class TicketSlaBreachedNotification extends Notification
{
    use Queueable;

    public $ticket;
    public $breachType; // 'response' or 'resolution'

    public function __construct(Ticket $ticket, string $breachType)
    {
        $this->ticket = $ticket;
        $this->breachType = $breachType;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        $typeLabel = $this->breachType === 'response' ? 'Respons Pertama' : 'Penyelesaian';
        return [
            'ticket_id' => $this->ticket->id,
            'title' => $this->ticket->title,
            'breach_type' => $this->breachType,
            'message' => 'SLA ' . $typeLabel . ' terlampaui untuk tiket: ' . $this->ticket->title,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $typeLabel = $this->breachType === 'response' ? 'Respons Pertama' : 'Penyelesaian';
        return new BroadcastMessage([
            'ticket_id' => $this->ticket->id,
            'title' => $this->ticket->title,
            'breach_type' => $this->breachType,
            'message' => 'SLA ' . $typeLabel . ' terlampaui untuk tiket: ' . $this->ticket->title,
        ]);
    }
}

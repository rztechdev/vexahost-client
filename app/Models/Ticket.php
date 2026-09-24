<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'title',
        'description',
        'priority',
        'status',
        'client_id',
        'technician_id',
        'sla_response_due_at',
        'sla_resolution_due_at',
        'first_response_at',
        'resolved_at',
        'sla_response_notified',
        'sla_resolution_notified',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sla_response_due_at' => 'datetime',
            'sla_resolution_due_at' => 'datetime',
            'first_response_at' => 'datetime',
            'resolved_at' => 'datetime',
            'sla_response_notified' => 'boolean',
            'sla_resolution_notified' => 'boolean',
        ];
    }

    /**
     * Get the client that created the ticket.
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the technician assigned to the ticket.
     */
    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    /**
     * Get the project associated with the ticket.
     */
    public function project()
    {
        return $this->hasOne(Project::class);
    }

    /**
     * Get SLA targets in minutes based on priority.
     * Returns [response_minutes, resolution_minutes]
     */
    public static function getSlaTargets(string $priority): array
    {
        return match ($priority) {
            'high' => [60, 240], // 1 hour, 4 hours
            'medium' => [240, 1440], // 4 hours, 24 hours
            'low' => [480, 4320], // 8 hours, 72 hours
            default => [240, 1440],
        };
    }

    /**
     * Check if response SLA is breached.
     */
    public function isResponseSlaBreached(): bool
    {
        if ($this->first_response_at && $this->sla_response_due_at) {
            return $this->first_response_at->greaterThan($this->sla_response_due_at);
        }
        
        return $this->sla_response_due_at && now()->greaterThan($this->sla_response_due_at);
    }

    /**
     * Check if resolution SLA is breached.
     */
    public function isResolutionSlaBreached(): bool
    {
        if ($this->resolved_at && $this->sla_resolution_due_at) {
            return $this->resolved_at->greaterThan($this->sla_resolution_due_at);
        }
        
        return $this->sla_resolution_due_at && now()->greaterThan($this->sla_resolution_due_at);
    }

    /**
     * Get current SLA status (ok, warning, breached).
     */
    public function statusLabel(): string
    {
        return app(\App\Services\TicketWorkflowService::class)->statusLabel($this->status);
    }

    /**
     * PIC yang ditampilkan ke client: teknisi tiket, atau manager proyek terkait.
     */
    public function displayTechnician(): ?User
    {
        $this->loadMissing(['technician', 'project.manager']);

        return $this->technician ?? $this->project?->manager;
    }

    public function slaStatus(): string
    {
        if ($this->isResolutionSlaBreached() || $this->isResponseSlaBreached()) {
            return 'breached';
        }
        
        // Warning status if 75% of resolution time has passed and not resolved
        if (!$this->resolved_at && $this->sla_resolution_due_at && $this->created_at) {
            $totalMinutes = $this->created_at->diffInMinutes($this->sla_resolution_due_at);
            $elapsedMinutes = $this->created_at->diffInMinutes(now());
            
            if ($totalMinutes > 0 && ($elapsedMinutes / $totalMinutes) >= 0.75) {
                return 'warning';
            }
        }
        
        return 'ok';
    }
}

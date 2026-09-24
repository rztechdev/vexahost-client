<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'project_id',
        'harga_bulanan',
        'status',
        'tanggal_mulai',
        'tanggal_jatuh_tempo_berikutnya',
        'terakhir_diingatkan_at',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'harga_bulanan' => 'integer',
            'tanggal_mulai' => 'date',
            'tanggal_jatuh_tempo_berikutnya' => 'date',
            'terakhir_diingatkan_at' => 'datetime',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * Check if reminder is due (H-3 before next due date).
     */
    public function isReminderDue(): bool
    {
        if ($this->status !== 'aktif' || !$this->tanggal_jatuh_tempo_berikutnya) {
            return false;
        }

        $reminderDate = $this->tanggal_jatuh_tempo_berikutnya->copy()->subDays(3);
        $today = now()->startOfDay();

        // If today is on or after H-3 and before/on due date, and hasn't been reminded today
        if ($today->greaterThanOrEqualTo($reminderDate->startOfDay())) {
            if (!$this->terakhir_diingatkan_at || $this->terakhir_diingatkan_at->diffInDays(now()) >= 20) {
                return true;
            }
        }

        return false;
    }
}

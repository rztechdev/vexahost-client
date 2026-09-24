<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Proyek gabungan Admin Panel (CRM) & Client Panel.
 *
 * - `status` adalah status siklus hidup CRM (draft, dp_diterima, dikerjakan, review, selesai, dibatalkan).
 * - `work_status` adalah status operasional turunan untuk Client Panel & Kanban
 *   (pending, active, completed, archived), dihitung dari `status`.
 * - Alias kolom lama CRM (`nama_project`, `tanggal_mulai`, `tanggal_selesai`) tetap bisa dipakai.
 */
class Project extends Model
{
    use HasFactory;

    /** Status CRM yang dianggap "aktif / sedang berjalan". */
    public const ACTIVE_STATUSES = ['dp_diterima', 'dikerjakan', 'review'];

    public const WORK_STATUSES = ['pending', 'active', 'completed', 'archived'];

    protected $fillable = [
        'lead_id',
        'ticket_id',
        'client_id',
        'manager_id',
        'name',
        'nama_project',
        'description',
        'paket',
        'harga',
        'status',
        'work_status',
        'start_date',
        'end_date',
        'tanggal_mulai',
        'tanggal_selesai',
        'link_website',
        'catatan',
        'client_provisioned_at',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'client_provisioned_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::updated(function (Project $project) {
            if ($project->wasChanged('status')) {
                $previousStatus = $project->ticket?->status;
                $project->syncLinkedTicketStatus();

                if ($project->ticket_id && $project->ticket) {
                    app(\App\Services\TicketWorkflowService::class)
                        ->afterLinkedTicketSynced($project->ticket->fresh(), $previousStatus);
                }
            }
        });
    }

    // ------------------------------------------------------------------
    // Alias kolom CRM lama
    // ------------------------------------------------------------------

    public function getNamaProjectAttribute(): ?string
    {
        return $this->attributes['name'] ?? null;
    }

    public function setNamaProjectAttribute($value): void
    {
        $this->attributes['name'] = $value;
    }

    public function getTanggalMulaiAttribute(): ?Carbon
    {
        return $this->start_date;
    }

    public function setTanggalMulaiAttribute($value): void
    {
        $this->attributes['start_date'] = $value ?: null;
    }

    public function getTanggalSelesaiAttribute(): ?Carbon
    {
        return $this->end_date;
    }

    public function setTanggalSelesaiAttribute($value): void
    {
        $this->attributes['end_date'] = $value ?: null;
    }

    // ------------------------------------------------------------------
    // Status operasional (Client Panel & Kanban)
    // ------------------------------------------------------------------

    public static function workStatusFor(?string $status): string
    {
        return match ($status) {
            'selesai' => 'completed',
            'dibatalkan' => 'archived',
            'dp_diterima', 'dikerjakan', 'review' => 'active',
            default => 'pending',
        };
    }

    /** Kumpulan status CRM untuk satu status operasional (untuk query). */
    public static function statusesForWork(string $workStatus): array
    {
        return match ($workStatus) {
            'completed' => ['selesai'],
            'archived' => ['dibatalkan'],
            'active' => self::ACTIVE_STATUSES,
            default => ['draft'],
        };
    }

    public function getWorkStatusAttribute(): string
    {
        return static::workStatusFor($this->attributes['status'] ?? null);
    }

    /**
     * Mengubah status operasional akan memetakan ke status CRM,
     * tanpa menurunkan status CRM yang sudah berada di kelompok yang sama.
     */
    public function setWorkStatusAttribute($value): void
    {
        $current = $this->attributes['status'] ?? 'draft';

        if (static::workStatusFor($current) === $value) {
            return;
        }

        $this->attributes['status'] = match ($value) {
            'completed' => 'selesai',
            'archived' => 'dibatalkan',
            'active' => 'dikerjakan',
            default => 'draft',
        };
    }

    public function scopeWhereWorkStatus($query, string $workStatus)
    {
        return $query->whereIn('status', static::statusesForWork($workStatus));
    }

    public function getWorkStatusLabelAttribute(): string
    {
        return match ($this->work_status) {
            'active' => 'Aktif',
            'completed' => 'Selesai',
            'archived' => 'Arsip',
            default => 'Pending',
        };
    }

    /**
     * Sinkronkan status tiket terkait berdasarkan status proyek.
     */
    public function syncLinkedTicketStatus(): void
    {
        if (! $this->ticket_id) {
            return;
        }

        $ticket = $this->ticket;
        if (! $ticket) {
            return;
        }

        if ($this->work_status === 'completed') {
            $updates = ['status' => 'resolved'];
            if (! $ticket->resolved_at) {
                $updates['resolved_at'] = now();
            }
            $ticket->update($updates);

            return;
        }

        if ($this->work_status === 'archived') {
            $ticket->update([
                'status' => 'closed',
                'resolved_at' => $ticket->resolved_at ?? now(),
            ]);
        }
    }

    // ------------------------------------------------------------------
    // Relasi
    // ------------------------------------------------------------------

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function latestInvoice(): HasOne
    {
        return $this->hasOne(Invoice::class)->latestOfMany();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'project_id');
    }

    public function maintenanceSubscription(): HasOne
    {
        return $this->hasOne(MaintenanceSubscription::class, 'project_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(ProjectSubscription::class, 'project_id');
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(ProjectSubscription::class, 'project_id')
            ->whereIn('status', ['aktif', 'akan_expired', 'diperpanjang'])
            ->latestOfMany();
    }

    // ------------------------------------------------------------------
    // Keuangan (CRM)
    // ------------------------------------------------------------------

    public function getTotalPaidAttribute(): int
    {
        return (int) $this->payments()->where('status', 'lunas')->sum('jumlah');
    }

    public function getRemainingBalanceAttribute(): int
    {
        return max(0, $this->harga - $this->total_paid);
    }

    public function getTotalTerbayarAttribute(): int
    {
        return $this->total_paid;
    }

    public function getSisaTagihanAttribute(): int
    {
        return $this->remaining_balance;
    }

    public function getPaymentStatusAttribute(): string
    {
        $paid = $this->total_paid;
        $total = $this->harga;

        if ($paid >= $total && $total > 0) {
            return 'lunas';
        }
        if ($paid > 0) {
            return 'dp_diterima';
        }

        return 'unpaid';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'Draft / Baru',
            'dp_diterima' => 'DP Diterima',
            'dikerjakan' => 'Sedang Dikerjakan',
            'review' => 'Review Klien',
            'selesai' => 'Selesai & Live',
            'dibatalkan' => 'Dibatalkan',
            default => ucfirst((string) $this->status),
        };
    }

    public function getPaketLabelAttribute(): string
    {
        return match ($this->paket) {
            'landing_page' => 'Landing Page',
            'company_profile' => 'Company Profile',
            'toko_kasir' => 'Toko & Kasir POS',
            'custom' => 'Custom Web App',
            default => ucfirst((string) $this->paket),
        };
    }

    // ------------------------------------------------------------------
    // Langganan (diturunkan dari project_subscriptions aktif)
    // ------------------------------------------------------------------

    public function getSubscriptionTypeAttribute(): ?string
    {
        return $this->activeSubscription?->tipe;
    }

    public function getSubscriptionPriceAttribute(): int
    {
        return (int) ($this->activeSubscription?->harga ?? 0);
    }

    public function getSubscriptionStartAttribute(): ?Carbon
    {
        return $this->activeSubscription?->tanggal_mulai;
    }

    public function getSubscriptionExpiredAttribute(): ?Carbon
    {
        return $this->activeSubscription?->tanggal_expired;
    }

    public function getSubscriptionStatusAttribute(): ?string
    {
        return $this->activeSubscription?->status;
    }

    public function getAutoRenewAttribute(): bool
    {
        return (bool) ($this->activeSubscription?->auto_renew ?? false);
    }

    public function getSubscriptionTypeLabelAttribute(): ?string
    {
        return match ($this->subscription_type) {
            'tahunan' => 'Tahunan',
            'bulanan' => 'Bulanan',
            '6_bulan' => '6 Bulan',
            'custom' => 'Custom',
            default => null,
        };
    }

    public function getSubscriptionStatusLabelAttribute(): ?string
    {
        return match ($this->subscription_status) {
            'aktif' => 'Aktif',
            'akan_expired' => 'Akan Expired',
            'expired' => 'Expired',
            'diperpanjang' => 'Diperpanjang',
            'nonaktif' => 'Nonaktif',
            default => null,
        };
    }

    public function getSubscriptionStatusColorAttribute(): string
    {
        return match ($this->subscription_status) {
            'aktif' => 'emerald',
            'akan_expired' => 'amber',
            'expired' => 'red',
            'diperpanjang' => 'sky',
            default => 'zinc',
        };
    }

    public function getSubscriptionSisaHariAttribute(): ?int
    {
        if (! $this->subscription_expired) {
            return null;
        }

        return (int) Carbon::now()->startOfDay()->diffInDays($this->subscription_expired, false);
    }

    public function hasActiveSubscription(): bool
    {
        return $this->subscription_type !== null
            && $this->subscription_status !== null
            && $this->subscription_status !== 'nonaktif';
    }

    /**
     * Persentase progres proyek (weighted lifecycle dari tugas Kanban).
     */
    public function getProgressPercentageAttribute(): int
    {
        if ($this->work_status === 'completed') {
            return 100;
        }

        $tasks = $this->tasks;
        if ($tasks->isEmpty()) {
            return match ($this->work_status) {
                'active' => 50,
                default => 0,
            };
        }

        $sum = 0;
        foreach ($tasks as $task) {
            $sum += match ($task->status) {
                'done' => 100,
                'review' => 85,
                'in_progress' => 50,
                'todo' => 10,
                default => 0,
            };
        }

        return (int) round($sum / $tasks->count());
    }
}

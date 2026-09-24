<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'lead_id',
        'tipe',
        'harga',
        'tanggal_mulai',
        'tanggal_expired',
        'status',
        'auto_renew',
        'terakhir_diingatkan_at',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'integer',
            'tanggal_mulai' => 'date',
            'tanggal_expired' => 'date',
            'auto_renew' => 'boolean',
            'terakhir_diingatkan_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function getTipeLabelAttribute(): string
    {
        return match ($this->tipe) {
            'tahunan' => '1 Tahun',
            'bulanan' => 'Bulanan',
            '6_bulan' => '6 Bulan',
            'custom' => 'Custom',
            default => ucfirst($this->tipe),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'aktif' => 'Aktif',
            'akan_expired' => 'Akan Expired',
            'expired' => 'Expired',
            'diperpanjang' => 'Diperpanjang',
            'nonaktif' => 'Nonaktif',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'aktif', 'diperpanjang' => 'green',
            'akan_expired' => 'yellow',
            'expired' => 'red',
            'nonaktif' => 'gray',
            default => 'gray',
        };
    }

    public function getSisaHariAttribute(): int
    {
        if (!$this->tanggal_expired) {
            return 0;
        }

        return max(0, (int) now()->startOfDay()->diffInDays($this->tanggal_expired, false));
    }

    public function isExpired(): bool
    {
        return $this->tanggal_expired && $this->tanggal_expired->isPast();
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        if (!$this->tanggal_expired || $this->status === 'nonaktif') {
            return false;
        }

        return $this->sisa_hari <= $days && $this->sisa_hari > 0;
    }

    public function isReminderDue(): bool
    {
        if (!in_array($this->status, ['aktif', 'akan_expired']) || !$this->tanggal_expired) {
            return false;
        }

        $sisaHari = $this->sisa_hari;
        $shouldRemind = in_array($sisaHari, [30, 7, 1]) || $sisaHari <= 0;

        if (!$shouldRemind) {
            return false;
        }

        if ($this->terakhir_diingatkan_at && $this->terakhir_diingatkan_at->diffInDays(now()) < 1) {
            return false;
        }

        return true;
    }

    public function renew(?string $tipe = null): self
    {
        $tipe = $tipe ?: $this->tipe;
        $durasi = match ($tipe) {
            'bulanan' => 1,
            '6_bulan' => 6,
            'tahunan' => 12,
            default => 12,
        };

        $startDate = $this->tanggal_expired->isPast() ? now() : $this->tanggal_expired;

        $this->update([
            'tipe' => $tipe,
            'tanggal_mulai' => $startDate->toDateString(),
            'tanggal_expired' => $startDate->copy()->addMonths($durasi)->toDateString(),
            'status' => 'diperpanjang',
            'terakhir_diingatkan_at' => null,
        ]);

        return $this;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_usaha',
        'nama_kontak',
        'kontak_wa',
        'email',
        'sumber',
        'status',
        'paket_diminati',
        'nilai_nego',
        'catatan',
        'follow_up_date',
    ];

    protected function casts(): array
    {
        return [
            'nilai_nego' => 'integer',
            'follow_up_date' => 'date',
        ];
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'lead_id');
    }

    public function activeProject(): HasOne
    {
        return $this->hasOne(Project::class, 'lead_id')->latestOfMany();
    }

    public function maintenanceSubscriptions(): HasMany
    {
        return $this->hasMany(MaintenanceSubscription::class, 'lead_id');
    }

    public function activeMaintenanceSubscription(): HasOne
    {
        return $this->hasOne(MaintenanceSubscription::class, 'lead_id')->where('status', 'aktif')->latestOfMany();
    }

    public function projectSubscriptions(): HasMany
    {
        return $this->hasMany(ProjectSubscription::class, 'lead_id');
    }

    public function messageLogs(): HasMany
    {
        return $this->hasMany(MessageLog::class, 'lead_id')->orderBy('created_at', 'asc');
    }

    /**
     * Check if follow-up is overdue.
     */
    public function isFollowUpOverdue(): bool
    {
        if (!$this->follow_up_date) {
            return false;
        }

        if (in_array($this->status, ['deal', 'tidak_lanjut'])) {
            return false;
        }

        return $this->follow_up_date->isPast() && !$this->follow_up_date->isToday();
    }

    /**
     * Check if follow-up is today.
     */
    public function isFollowUpToday(): bool
    {
        if (!$this->follow_up_date) {
            return false;
        }

        if (in_array($this->status, ['deal', 'tidak_lanjut'])) {
            return false;
        }

        return $this->follow_up_date->isToday();
    }

    /**
     * Helper to get formatted sumber label.
     */
    public function getSumberLabelAttribute(): string
    {
        return match ($this->sumber) {
            'warm_network' => 'Warm Network',
            'cold_outreach' => 'Cold Outreach',
            'komunitas' => 'Komunitas',
            'marketplace' => 'Marketplace',
            'referral' => 'Referral',
            'website' => 'Website',
            default => 'Lainnya',
        };
    }

    /**
     * Helper to get formatted status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'belum_dihubungi' => 'Belum Dihubungi',
            'sudah_chat' => 'Sudah Chat',
            'nego' => 'Nego',
            'deal' => 'Deal (Klien)',
            'tidak_lanjut' => 'Tidak Lanjut',
            default => ucfirst($this->status),
        };
    }

    /**
     * Helper to get formatted paket label.
     */
    public function getPaketLabelAttribute(): string
    {
        return match ($this->paket_diminati) {
            'landing_page' => 'Landing Page (Rp 499rb)',
            'company_profile' => 'Company Profile (Rp 999rb)',
            'toko_kasir' => 'Toko & Kasir POS (Rp 1.5jt)',
            'custom' => 'Custom Web App',
            'belum_tahu' => 'Belum Tahu / Konsultasi',
            default => ucfirst($this->paket_diminati),
        };
    }

    /**
     * Helper to get default price for interested package.
     */
    public function getDefaultPackagePrice(): int
    {
        $packages = config('whatsapp.packages', []);
        return $packages[$this->paket_diminati]['price'] ?? 499000;
    }

    /**
     * Helper to get estimated deal / negotiated price.
     * Prioritizes negotiated price if set, otherwise falls back to package price.
     */
    public function getEstimatedDealPrice(): int
    {
        if ($this->nilai_nego && $this->nilai_nego > 0) {
            return (int) $this->nilai_nego;
        }

        return $this->getDefaultPackagePrice();
    }
}

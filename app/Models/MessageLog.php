<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'kontak_wa',
        'arah',
        'tipe_pesan',
        'isi_pesan',
        'status_kirim',
        'response_payload',
    ];

    protected function casts(): array
    {
        return [
            'response_payload' => 'array',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }
}

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'avatar',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Role staf internal yang mengakses Admin Panel.
     */
    public const STAFF_ROLES = ['admin', 'ceo', 'sales', 'project_manager', 'finance', 'technician'];

    /**
     * Klien = pengguna dengan role `client` dan tanpa role staf.
     */
    public function isClient(): bool
    {
        return $this->hasRole('client') && ! $this->hasAnyRole(self::STAFF_ROLES);
    }

    public function isStaff(): bool
    {
        return ! $this->isClient();
    }

    /**
     * URL dashboard sesuai panel pengguna.
     */
    public function homeUrl(): string
    {
        return $this->isClient() ? route('client.dashboard') : route('admin.dashboard');
    }

    /**
     * Proyek milik klien ini.
     */
    public function clientProjects()
    {
        return $this->hasMany(Project::class, 'client_id');
    }

    /**
     * Get the tickets created by this client.
     */
    public function clientTickets()
    {
        return $this->hasMany(Ticket::class, 'client_id');
    }

    /**
     * Get the tickets assigned to this technician.
     */
    public function assignedTickets()
    {
        return $this->hasMany(Ticket::class, 'technician_id');
    }

    /**
     * Get the invoices belonging to this client.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'client_id');
    }
}

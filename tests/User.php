<?php

namespace Escalated\Filament\Tests;

use Escalated\Laravel\Contracts\Ticketable;
use Escalated\Laravel\Models\Ticket;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser, Ticketable
{
    use HasFactory, Notifiable;

    protected $guarded = ['id'];

    protected $hidden = ['password', 'remember_token'];

    public function getTicketableNameAttribute(): string
    {
        return $this->name;
    }

    public function getTicketableEmailAttribute(): string
    {
        return $this->email;
    }

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    /**
     * Relationship used by escalated-laravel Reports page (getTicketsByAgent).
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }
}

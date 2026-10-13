<?php

namespace Escalated\Filament\Support\Concerns;

use Escalated\Filament\Support\TicketAccess;
use Illuminate\Database\Eloquent\Model;

trait AuthorizesTicketOwner
{
    public function getOwnerRecord(): Model
    {
        // Livewire may hydrate a record loaded before membership was revoked.
        return TicketAccess::find(parent::getOwnerRecord()->getKey());
    }
}

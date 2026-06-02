<?php

namespace Escalated\Filament\Tests\Models;

use Escalated\Laravel\Concerns\PresentsAsTicketSubject;
use Escalated\Laravel\Contracts\TicketSubject;
use Illuminate\Database\Eloquent\Model;

class FakeProject extends Model implements TicketSubject
{
    use PresentsAsTicketSubject;

    protected $table = 'fake_projects';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    public function ticketSubjectSubtitle(): ?string
    {
        return $this->account ? 'Project · '.$this->account : null;
    }
}

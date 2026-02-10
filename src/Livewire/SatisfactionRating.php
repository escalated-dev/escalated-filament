<?php

namespace Escalated\Filament\Livewire;

use Escalated\Laravel\Models\SatisfactionRating as SatisfactionRatingModel;
use Escalated\Laravel\Models\Ticket;
use Livewire\Component;

class SatisfactionRating extends Component
{
    public int $ticketId;

    public ?int $rating = null;

    public ?string $comment = null;

    public bool $hasRating = false;

    public function mount(int $ticketId): void
    {
        $this->ticketId = $ticketId;

        $existing = SatisfactionRatingModel::where('ticket_id', $ticketId)->first();

        if ($existing) {
            $this->rating = $existing->rating;
            $this->comment = $existing->comment;
            $this->hasRating = true;
        }
    }

    public function render()
    {
        return view('escalated-filament::livewire.satisfaction-rating');
    }
}

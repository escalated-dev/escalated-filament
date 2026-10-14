<?php

namespace Escalated\Filament\Support;

use Escalated\Laravel\Escalated;
use Escalated\Laravel\Models\Ticket;
use Escalated\Laravel\Support\TicketAccounts;
use Escalated\Laravel\Support\TicketVisibility;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

final class TicketAccess
{
    public static function find(int|string $id, string $ability = 'view'): Ticket
    {
        abort_unless(PanelAccess::agent(), 403);
        $ticket = Ticket::findOrFail($id);
        Gate::authorize($ability, $ticket);

        return $ticket;
    }

    public static function prospective(array $data): Ticket
    {
        $ticket = new Ticket($data);
        if ($account = TicketAccounts::resolve(auth()->user())) {
            $ticket->account()->associate($account);
        }

        return $ticket;
    }

    public static function assignmentOptions(Ticket $ticket): array
    {
        return array_filter(Escalated::userOptions(), fn ($id) => TicketVisibility::agentCanAccess($id, $ticket), ARRAY_FILTER_USE_KEY);
    }

    public static function bulkAssignmentOptions(iterable $tickets): array
    {
        $options = Escalated::userOptions();
        foreach ($tickets as $ticket) {
            $options = array_intersect_key($options, self::assignmentOptions($ticket));
        }

        return $options;
    }

    public static function validateAssignee(Ticket $ticket, mixed $id, string $field = 'agent_id'): void
    {
        if ($id !== null && $id !== '' && ! TicketVisibility::agentCanAccess($id, $ticket)) {
            throw ValidationException::withMessages([$field => 'This agent cannot access the ticket.']);
        }
    }
}

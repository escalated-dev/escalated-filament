<?php

namespace Escalated\Filament\Resources\TicketResource\Pages;

use Escalated\Filament\Resources\TicketResource;
use Escalated\Filament\Support\TicketAccess;
use Filament\Resources\Pages\CreateRecord;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['reference']);
        $data['requester_type'] = get_class(auth()->user());
        $data['requester_id'] = auth()->id();
        $data['status'] = 'open';
        $data['channel'] = 'web';
        TicketAccess::validateAssignee(TicketAccess::prospective($data), $data['assigned_to'] ?? null, 'data.assigned_to');

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}

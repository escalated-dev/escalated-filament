<?php

namespace Escalated\Filament\Resources\CannedResponseResource\Pages;

use Escalated\Filament\Resources\CannedResponseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCannedResponses extends ListRecords
{
    protected static string $resource = CannedResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

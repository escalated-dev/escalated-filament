<?php

namespace Escalated\Filament\Resources\SlaPolicyResource\Pages;

use Escalated\Filament\Resources\SlaPolicyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSlaPolicies extends ListRecords
{
    protected static string $resource = SlaPolicyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

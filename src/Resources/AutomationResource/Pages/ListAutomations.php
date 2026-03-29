<?php

namespace Escalated\Filament\Resources\AutomationResource\Pages;

use Escalated\Filament\Resources\AutomationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAutomations extends ListRecords
{
    protected static string $resource = AutomationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

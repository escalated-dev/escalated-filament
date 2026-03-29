<?php

namespace Escalated\Filament\Resources\BusinessScheduleResource\Pages;

use Escalated\Filament\Resources\BusinessScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBusinessSchedules extends ListRecords
{
    protected static string $resource = BusinessScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

<?php

namespace Escalated\Filament\Resources\BusinessScheduleResource\Pages;

use Escalated\Filament\Resources\BusinessScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBusinessSchedule extends EditRecord
{
    protected static string $resource = BusinessScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

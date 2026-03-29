<?php

namespace Escalated\Filament\Resources\BusinessScheduleResource\Pages;

use Escalated\Filament\Resources\BusinessScheduleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBusinessSchedule extends CreateRecord
{
    protected static string $resource = BusinessScheduleResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

<?php

namespace Escalated\Filament\Resources\SlaPolicyResource\Pages;

use Escalated\Filament\Resources\SlaPolicyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSlaPolicy extends CreateRecord
{
    protected static string $resource = SlaPolicyResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

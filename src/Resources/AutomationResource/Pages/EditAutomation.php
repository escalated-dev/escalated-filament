<?php

namespace Escalated\Filament\Resources\AutomationResource\Pages;

use Escalated\Filament\Resources\AutomationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAutomation extends EditRecord
{
    protected static string $resource = AutomationResource::class;

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

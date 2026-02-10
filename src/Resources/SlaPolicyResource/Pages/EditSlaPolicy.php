<?php

namespace Escalated\Filament\Resources\SlaPolicyResource\Pages;

use Escalated\Filament\Resources\SlaPolicyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSlaPolicy extends EditRecord
{
    protected static string $resource = SlaPolicyResource::class;

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

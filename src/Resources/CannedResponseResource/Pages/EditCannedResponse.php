<?php

namespace Escalated\Filament\Resources\CannedResponseResource\Pages;

use Escalated\Filament\Resources\CannedResponseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCannedResponse extends EditRecord
{
    protected static string $resource = CannedResponseResource::class;

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

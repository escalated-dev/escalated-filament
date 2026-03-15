<?php

namespace Escalated\Filament\Resources\ApiTokenResource\Pages;

use Escalated\Filament\Resources\ApiTokenResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApiToken extends EditRecord
{
    protected static string $resource = ApiTokenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Revoke Token')
                ->modalHeading('Revoke API Token')
                ->modalDescription('Are you sure you want to revoke this token? Any applications using this token will immediately lose access.')
                ->modalSubmitActionLabel('Revoke Token'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

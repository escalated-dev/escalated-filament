<?php

namespace Escalated\Filament\Resources\WebhookResource\Pages;

use Escalated\Filament\Resources\WebhookResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWebhook extends EditRecord
{
    protected static string $resource = WebhookResource::class;

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

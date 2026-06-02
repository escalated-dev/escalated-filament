<?php

namespace Escalated\Filament\Resources\NewsletterTemplateResource\Pages;

use Escalated\Filament\Resources\NewsletterTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNewsletterTemplate extends EditRecord
{
    protected static string $resource = NewsletterTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

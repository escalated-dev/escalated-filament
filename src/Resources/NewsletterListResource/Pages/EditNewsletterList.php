<?php

namespace Escalated\Filament\Resources\NewsletterListResource\Pages;

use Escalated\Filament\Resources\NewsletterListResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNewsletterList extends EditRecord
{
    protected static string $resource = NewsletterListResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

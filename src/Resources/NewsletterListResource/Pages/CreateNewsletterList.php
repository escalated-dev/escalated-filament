<?php

namespace Escalated\Filament\Resources\NewsletterListResource\Pages;

use Escalated\Filament\Resources\NewsletterListResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNewsletterList extends CreateRecord
{
    protected static string $resource = NewsletterListResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

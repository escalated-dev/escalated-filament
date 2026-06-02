<?php

namespace Escalated\Filament\Resources\NewsletterTemplateResource\Pages;

use Escalated\Filament\Resources\NewsletterTemplateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNewsletterTemplate extends CreateRecord
{
    protected static string $resource = NewsletterTemplateResource::class;

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

<?php

namespace Escalated\Filament\Resources\NewsletterTemplateResource\Pages;

use Escalated\Filament\Resources\NewsletterTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNewsletterTemplates extends ListRecords
{
    protected static string $resource = NewsletterTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}

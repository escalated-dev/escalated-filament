<?php

namespace Escalated\Filament\Resources\MacroResource\Pages;

use Escalated\Filament\Resources\MacroResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMacro extends CreateRecord
{
    protected static string $resource = MacroResource::class;

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

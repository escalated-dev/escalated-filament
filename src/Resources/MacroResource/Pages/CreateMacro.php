<?php

namespace Escalated\Filament\Resources\MacroResource\Pages;

use Escalated\Filament\Resources\MacroResource;
use Escalated\Filament\Resources\MacroResource\Concerns\NormalizesMacroActionMessages;
use Filament\Resources\Pages\CreateRecord;

class CreateMacro extends CreateRecord
{
    use NormalizesMacroActionMessages;

    protected static string $resource = MacroResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = $this->normalizeMacroActionsForStorage($data);
        $data['created_by'] = auth()->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

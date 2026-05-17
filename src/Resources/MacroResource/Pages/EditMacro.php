<?php

namespace Escalated\Filament\Resources\MacroResource\Pages;

use Escalated\Filament\Resources\MacroResource;
use Escalated\Filament\Resources\MacroResource\Concerns\NormalizesMacroActionMessages;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMacro extends EditRecord
{
    use NormalizesMacroActionMessages;

    protected static string $resource = MacroResource::class;

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

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $this->normalizeMacroActionsForForm($data);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->normalizeMacroActionsForStorage($data);
    }
}

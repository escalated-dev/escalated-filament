<?php

namespace Escalated\Filament\Resources\MacroResource\Pages;

use Escalated\Filament\Resources\MacroResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMacro extends EditRecord
{
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
}

<?php

namespace Escalated\Filament\Resources\MacroResource\Pages;

use Escalated\Filament\Resources\MacroResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMacros extends ListRecords
{
    protected static string $resource = MacroResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

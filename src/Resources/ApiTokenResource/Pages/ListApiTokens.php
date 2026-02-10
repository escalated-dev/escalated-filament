<?php

namespace Escalated\Filament\Resources\ApiTokenResource\Pages;

use Escalated\Filament\Resources\ApiTokenResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApiTokens extends ListRecords
{
    protected static string $resource = ApiTokenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

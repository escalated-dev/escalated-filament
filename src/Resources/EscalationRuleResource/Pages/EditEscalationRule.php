<?php

namespace Escalated\Filament\Resources\EscalationRuleResource\Pages;

use Escalated\Filament\Resources\EscalationRuleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEscalationRule extends EditRecord
{
    protected static string $resource = EscalationRuleResource::class;

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

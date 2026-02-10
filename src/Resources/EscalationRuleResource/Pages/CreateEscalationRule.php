<?php

namespace Escalated\Filament\Resources\EscalationRuleResource\Pages;

use Escalated\Filament\Resources\EscalationRuleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEscalationRule extends CreateRecord
{
    protected static string $resource = EscalationRuleResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

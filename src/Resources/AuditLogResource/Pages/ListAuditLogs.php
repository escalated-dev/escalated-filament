<?php

namespace Escalated\Filament\Resources\AuditLogResource\Pages;

use Escalated\Filament\Resources\AuditLogResource;
use Filament\Resources\Pages\ListRecords;

class ListAuditLogs extends ListRecords
{
    protected static string $resource = AuditLogResource::class;
}

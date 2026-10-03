<?php

namespace Escalated\Filament\Support\Concerns;

use Escalated\Filament\Support\PanelAccess;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/**
 * Agent resource (agents and admins). See RequiresAdminSeat for why the
 * check sits on getAuthorizationResponse(); the model policy still applies
 * after it.
 */
trait RequiresAgentSeat
{
    public static function getAuthorizationResponse(string|UnitEnum $action, ?Model $record = null): Response
    {
        if (! PanelAccess::agent()) {
            return Response::deny();
        }

        return parent::getAuthorizationResponse($action, $record);
    }
}

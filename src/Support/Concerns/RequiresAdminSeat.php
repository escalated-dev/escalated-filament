<?php

namespace Escalated\Filament\Support\Concerns;

use Escalated\Filament\Support\PanelAccess;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/**
 * Admin-only resource. Every Filament ability (viewAny, create, update,
 * delete, the bulk *Any variants, ...) resolves through
 * getAuthorizationResponse(), so denying here closes the list, create and
 * edit pages, table actions and global search together. A staff user still
 * goes through the model policy, when escalated-laravel ships one.
 */
trait RequiresAdminSeat
{
    public static function getAuthorizationResponse(string|UnitEnum $action, ?Model $record = null): Response
    {
        if (! PanelAccess::admin()) {
            return Response::deny();
        }

        return parent::getAuthorizationResponse($action, $record);
    }
}

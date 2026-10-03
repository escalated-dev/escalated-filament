<?php

namespace Escalated\Filament\Support;

use Escalated\Laravel\Support\StaffAccess;
use Escalated\Laravel\Tenancy\TenantContext;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;

/**
 * Staff checks for the panel. Delegates to escalated-laravel's StaffAccess,
 * which in tenant mode also requires a tenant-local seat from the host
 * resolver, so a global host flag never makes a customer of one account its
 * agent. Releases of escalated-laravel without StaffAccess fall back to the
 * configured host gate, which is what they enforce themselves.
 */
final class StaffSeat
{
    public static function isAgent(mixed $user): bool
    {
        return self::allows($user, 'agent');
    }

    public static function isAdmin(mixed $user): bool
    {
        return self::allows($user, 'admin');
    }

    /** Whether escalated-laravel is running in tenant (account) mode. */
    public static function tenancyEnabled(): bool
    {
        return class_exists(TenantContext::class) && app(TenantContext::class)->enabled();
    }

    /** @param  'agent'|'admin'  $seat */
    private static function allows(mixed $user, string $seat): bool
    {
        if (! $user instanceof Authenticatable) {
            return false;
        }

        if (class_exists(StaffAccess::class)) {
            return $seat === 'admin' ? StaffAccess::isAdmin($user) : StaffAccess::isAgent($user);
        }

        return Gate::forUser($user)->allows(config("escalated.authorization.{$seat}_gate", "escalated-{$seat}"));
    }
}

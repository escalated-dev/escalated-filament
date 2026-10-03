<?php

namespace Escalated\Filament\Support;

use Escalated\Laravel\Support\StaffAccess;
use Escalated\Laravel\Tenancy\TenantContext;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

/**
 * Staff checks for the panel. Delegates to escalated-laravel's StaffAccess,
 * which in tenant mode also requires a tenant-local seat from the host
 * resolver, so a global host flag never makes a customer of one account its
 * agent. Releases of escalated-laravel without StaffAccess fall back to the
 * configured host gate, which is what they enforce themselves.
 *
 * A panel can name its own gate (EscalatedFilamentPlugin::agentGate() /
 * adminGate()). When that differs from escalated-laravel's configured gate,
 * the named gate is checked instead, still behind the tenant-local seat.
 */
final class StaffSeat
{
    public static function isAgent(mixed $user, ?string $gate = null): bool
    {
        return self::allows($user, 'agent', $gate);
    }

    public static function isAdmin(mixed $user, ?string $gate = null): bool
    {
        return self::allows($user, 'admin', $gate);
    }

    /** Whether escalated-laravel is running in tenant (account) mode. */
    public static function tenancyEnabled(): bool
    {
        return class_exists(TenantContext::class) && app(TenantContext::class)->enabled();
    }

    /** @param  'agent'|'admin'  $seat */
    private static function allows(mixed $user, string $seat, ?string $gate): bool
    {
        if (! $user instanceof Authenticatable) {
            return false;
        }

        $configuredGate = config("escalated.authorization.{$seat}_gate", "escalated-{$seat}");

        if ($gate !== null && $gate !== $configuredGate) {
            return Gate::forUser($user)->allows($gate) && self::hasTenantSeat($user, $seat);
        }

        if (class_exists(StaffAccess::class)) {
            return $seat === 'admin' ? StaffAccess::isAdmin($user) : StaffAccess::isAgent($user);
        }

        return Gate::forUser($user)->allows($configuredGate);
    }

    /**
     * The tenant-local half of StaffAccess, for a gate StaffAccess does not
     * know about. Always true outside tenant mode; fails closed when tenant
     * mode is on but the installed escalated-laravel cannot answer.
     *
     * @param  'agent'|'admin'  $seat
     */
    private static function hasTenantSeat(Authenticatable $user, string $seat): bool
    {
        if (! self::tenancyEnabled()) {
            return true;
        }

        $context = app(TenantContext::class);

        return $user instanceof Model
            && method_exists($context, 'hasStaffSeat')
            && $context->hasStaffSeat($user, $seat);
    }
}

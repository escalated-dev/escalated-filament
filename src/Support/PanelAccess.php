<?php

namespace Escalated\Filament\Support;

use Escalated\Filament\EscalatedFilamentPlugin;
use Filament\Facades\Filament;

/**
 * Who may use which part of the Escalated panel. The host's canAccessPanel()
 * only decides who gets into the panel at all; these checks decide what a
 * panel user may do inside it, using the gates configured on the plugin.
 *
 * Mirrors escalated-laravel's routes: ticket handling (and canned responses,
 * whose policy is agent-level) is open to agents and admins, everything else
 * is admin-only.
 */
final class PanelAccess
{
    /** Agent surfaces: tickets, canned responses, the support dashboard. */
    public static function agent(): bool
    {
        $user = Filament::auth()->user();

        return StaffSeat::isAgent($user, self::plugin()->getAgentGate()) || self::admin();
    }

    /** Configuration surfaces: settings, automation, integrations, reports. */
    public static function admin(): bool
    {
        return StaffSeat::isAdmin(Filament::auth()->user(), self::plugin()->getAdminGate());
    }

    private static function plugin(): EscalatedFilamentPlugin
    {
        return app(EscalatedFilamentPlugin::class);
    }
}

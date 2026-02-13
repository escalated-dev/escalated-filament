<?php

namespace Escalated\Filament\Widgets;

use Escalated\Laravel\Enums\TicketPriority;
use Escalated\Laravel\Enums\TicketStatus;
use Escalated\Laravel\Models\SatisfactionRating;
use Escalated\Laravel\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TicketStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $myOpen = Ticket::where('assigned_to', auth()->id())->open()->count();
        $unassigned = Ticket::unassigned()->open()->count();
        $totalOpen = Ticket::open()->count();
        $breachedSla = Ticket::open()->breachedSla()->count();
        $resolvedToday = Ticket::where('resolved_at', '>=', now()->startOfDay())->count();
        $avgCsat = SatisfactionRating::whereHas('ticket', function ($q) {
            $q->where('created_at', '>=', now()->subDays(30));
        })->avg('rating');

        return [
            Stat::make(__('escalated-filament::filament.widgets.stats.my_open_tickets'), $myOpen)
                ->description(__('escalated-filament::filament.widgets.stats.assigned_to_you'))
                ->icon('heroicon-o-user')
                ->color('primary'),

            Stat::make(__('escalated-filament::filament.widgets.stats.unassigned'), $unassigned)
                ->description(__('escalated-filament::filament.widgets.stats.awaiting_assignment'))
                ->icon('heroicon-o-user-minus')
                ->color($unassigned > 0 ? 'warning' : 'success'),

            Stat::make(__('escalated-filament::filament.widgets.stats.total_open'), $totalOpen)
                ->description(__('escalated-filament::filament.widgets.stats.all_open_tickets'))
                ->icon('heroicon-o-inbox')
                ->color('info'),

            Stat::make(__('escalated-filament::filament.widgets.stats.sla_breached'), $breachedSla)
                ->description(__('escalated-filament::filament.widgets.stats.breached_sla_description'))
                ->icon('heroicon-o-exclamation-triangle')
                ->color($breachedSla > 0 ? 'danger' : 'success'),

            Stat::make(__('escalated-filament::filament.widgets.stats.resolved_today'), $resolvedToday)
                ->description(__('escalated-filament::filament.widgets.stats.resolved_today_description'))
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make(__('escalated-filament::filament.widgets.stats.avg_csat_30d'), $avgCsat ? number_format($avgCsat, 1).'/5' : 'N/A')
                ->description(__('escalated-filament::filament.widgets.stats.avg_csat_description'))
                ->icon('heroicon-o-star')
                ->color($avgCsat && $avgCsat >= 4 ? 'success' : ($avgCsat && $avgCsat >= 3 ? 'warning' : 'danger')),
        ];
    }
}

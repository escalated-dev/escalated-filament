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
            Stat::make('My Open Tickets', $myOpen)
                ->description('Assigned to you')
                ->icon('heroicon-o-user')
                ->color('primary'),

            Stat::make('Unassigned', $unassigned)
                ->description('Awaiting assignment')
                ->icon('heroicon-o-user-minus')
                ->color($unassigned > 0 ? 'warning' : 'success'),

            Stat::make('Total Open', $totalOpen)
                ->description('All open tickets')
                ->icon('heroicon-o-inbox')
                ->color('info'),

            Stat::make('SLA Breached', $breachedSla)
                ->description('Breached response or resolution SLA')
                ->icon('heroicon-o-exclamation-triangle')
                ->color($breachedSla > 0 ? 'danger' : 'success'),

            Stat::make('Resolved Today', $resolvedToday)
                ->description('Resolved in the last 24 hours')
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Avg CSAT (30d)', $avgCsat ? number_format($avgCsat, 1).'/5' : 'N/A')
                ->description('Average customer satisfaction')
                ->icon('heroicon-o-star')
                ->color($avgCsat && $avgCsat >= 4 ? 'success' : ($avgCsat && $avgCsat >= 3 ? 'warning' : 'danger')),
        ];
    }
}

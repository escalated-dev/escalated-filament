<?php

namespace Escalated\Filament\Pages;

use Escalated\Filament\EscalatedFilamentPlugin;
use Escalated\Filament\Widgets\CsatOverviewWidget;
use Escalated\Filament\Widgets\RecentTicketsWidget;
use Escalated\Filament\Widgets\SlaBreachWidget;
use Escalated\Filament\Widgets\TicketsByPriorityChart;
use Escalated\Filament\Widgets\TicketsByStatusChart;
use Escalated\Filament\Widgets\TicketStatsOverview;
use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?int $navigationSort = 0;

    protected static ?string $title = 'Support Dashboard';

    protected static ?string $slug = 'support-dashboard';

    protected static string $view = 'escalated-filament::pages.dashboard';

    public static function getNavigationGroup(): ?string
    {
        return app(EscalatedFilamentPlugin::class)->getNavigationGroup();
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TicketStatsOverview::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            TicketsByStatusChart::class,
            TicketsByPriorityChart::class,
            CsatOverviewWidget::class,
            RecentTicketsWidget::class,
            SlaBreachWidget::class,
        ];
    }

    protected function getFooterWidgetsColumns(): int|array
    {
        return 2;
    }
}

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
    protected static ?int $navigationSort = 0;

    protected static ?string $title = null;

    protected static ?string $slug = 'support-dashboard';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-home';
    }

    public function getView(): string
    {
        return 'escalated-filament::pages.dashboard';
    }

    public function getTitle(): string
    {
        return __('escalated-filament::filament.pages.dashboard.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('escalated-filament::filament.pages.dashboard.title');
    }

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

    public function getFooterWidgetsColumns(): int|array
    {
        return 2;
    }
}

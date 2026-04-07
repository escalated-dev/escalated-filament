<?php

namespace Escalated\Filament;

use Escalated\Filament\Widgets\CsatOverviewWidget;
use Escalated\Filament\Widgets\RecentTicketsWidget;
use Escalated\Filament\Widgets\SlaBreachWidget;
use Escalated\Filament\Widgets\TicketsByPriorityChart;
use Escalated\Filament\Widgets\TicketsByStatusChart;
use Escalated\Filament\Widgets\TicketStatsOverview;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class EscalatedFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(EscalatedFilamentPlugin::class);
    }

    public function boot(): void
    {
        if (! config('escalated.ui.enabled', true)) {
            return;
        }

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'escalated-filament');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'escalated-filament');

        Livewire::component('escalated.filament.widgets.ticket-stats-overview', TicketStatsOverview::class);
        Livewire::component('escalated.filament.widgets.tickets-by-status-chart', TicketsByStatusChart::class);
        Livewire::component('escalated.filament.widgets.tickets-by-priority-chart', TicketsByPriorityChart::class);
        Livewire::component('escalated.filament.widgets.csat-overview-widget', CsatOverviewWidget::class);
        Livewire::component('escalated.filament.widgets.recent-tickets-widget', RecentTicketsWidget::class);
        Livewire::component('escalated.filament.widgets.sla-breach-widget', SlaBreachWidget::class);

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/escalated-filament'),
            ], 'escalated-filament-views');

            $this->publishes([
                __DIR__.'/../resources/lang' => $this->app->langPath('vendor/escalated-filament'),
            ], 'escalated-filament-lang');
        }
    }
}

<?php

namespace Escalated\Filament;

use Escalated\Filament\Pages\Dashboard;
use Escalated\Filament\Pages\Reports;
use Escalated\Filament\Pages\Settings;
use Escalated\Filament\Resources\ApiTokenResource;
use Escalated\Filament\Resources\CannedResponseResource;
use Escalated\Filament\Resources\DepartmentResource;
use Escalated\Filament\Resources\EscalationRuleResource;
use Escalated\Filament\Resources\MacroResource;
use Escalated\Filament\Resources\SlaPolicyResource;
use Escalated\Filament\Resources\TagResource;
use Escalated\Filament\Resources\TicketResource;
use Escalated\Filament\Widgets\CsatOverviewWidget;
use Escalated\Filament\Widgets\RecentTicketsWidget;
use Escalated\Filament\Widgets\SlaBreachWidget;
use Escalated\Filament\Widgets\TicketsByPriorityChart;
use Escalated\Filament\Widgets\TicketsByStatusChart;
use Escalated\Filament\Widgets\TicketStatsOverview;
use Filament\Contracts\Plugin;
use Filament\Panel;

class EscalatedFilamentPlugin implements Plugin
{
    protected string $navigationGroup = 'Support';

    protected string $agentGate = 'escalated-agent';

    protected string $adminGate = 'escalated-admin';

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function getId(): string
    {
        return 'escalated';
    }

    public function navigationGroup(string $group): static
    {
        $this->navigationGroup = $group;

        return $this;
    }

    public function getNavigationGroup(): string
    {
        return $this->navigationGroup;
    }

    public function agentGate(string $gate): static
    {
        $this->agentGate = $gate;

        return $this;
    }

    public function getAgentGate(): string
    {
        return $this->agentGate;
    }

    public function adminGate(string $gate): static
    {
        $this->adminGate = $gate;

        return $this;
    }

    public function getAdminGate(): string
    {
        return $this->adminGate;
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                TicketResource::class,
                DepartmentResource::class,
                TagResource::class,
                SlaPolicyResource::class,
                EscalationRuleResource::class,
                CannedResponseResource::class,
                MacroResource::class,
                ApiTokenResource::class,
            ])
            ->pages([
                Dashboard::class,
                Reports::class,
                Settings::class,
            ])
            ->widgets([
                TicketStatsOverview::class,
                TicketsByStatusChart::class,
                TicketsByPriorityChart::class,
                CsatOverviewWidget::class,
                RecentTicketsWidget::class,
                SlaBreachWidget::class,
            ])
            ->livewireComponents([
                Livewire\TicketConversation::class,
                Livewire\SatisfactionRating::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}

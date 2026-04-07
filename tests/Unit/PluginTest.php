<?php

use Escalated\Filament\EscalatedFilamentPlugin;
use Escalated\Filament\Pages\Dashboard;
use Escalated\Filament\Pages\Reports;
use Escalated\Filament\Pages\Settings;
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
use Filament\Facades\Filament;

it('has the correct plugin ID', function () {
    $plugin = EscalatedFilamentPlugin::make();

    expect($plugin->getId())->toBe('escalated');
});

it('creates an instance via make()', function () {
    $plugin = EscalatedFilamentPlugin::make();

    expect($plugin)->toBeInstanceOf(EscalatedFilamentPlugin::class);
});

it('implements the Filament Plugin contract', function () {
    $plugin = EscalatedFilamentPlugin::make();

    expect($plugin)->toBeInstanceOf(Plugin::class);
});

it('has default navigation group of Support', function () {
    $plugin = EscalatedFilamentPlugin::make();

    expect($plugin->getNavigationGroup())->toBe('Support');
});

it('allows configuring navigation group', function () {
    $plugin = EscalatedFilamentPlugin::make()
        ->navigationGroup('Help Desk');

    expect($plugin->getNavigationGroup())->toBe('Help Desk');
});

it('has default agent gate', function () {
    $plugin = EscalatedFilamentPlugin::make();

    expect($plugin->getAgentGate())->toBe('escalated-agent');
});

it('allows configuring agent gate', function () {
    $plugin = EscalatedFilamentPlugin::make()
        ->agentGate('custom-agent-gate');

    expect($plugin->getAgentGate())->toBe('custom-agent-gate');
});

it('has default admin gate', function () {
    $plugin = EscalatedFilamentPlugin::make();

    expect($plugin->getAdminGate())->toBe('escalated-admin');
});

it('allows configuring admin gate', function () {
    $plugin = EscalatedFilamentPlugin::make()
        ->adminGate('custom-admin-gate');

    expect($plugin->getAdminGate())->toBe('custom-admin-gate');
});

it('supports fluent configuration chaining', function () {
    $plugin = EscalatedFilamentPlugin::make()
        ->navigationGroup('Custom Group')
        ->agentGate('my-agent-gate')
        ->adminGate('my-admin-gate');

    expect($plugin->getNavigationGroup())->toBe('Custom Group')
        ->and($plugin->getAgentGate())->toBe('my-agent-gate')
        ->and($plugin->getAdminGate())->toBe('my-admin-gate');
});

it('registers resources with the panel', function () {
    $panel = Filament::getDefaultPanel();
    $resources = $panel->getResources();

    expect($resources)->toContain(TicketResource::class)
        ->and($resources)->toContain(DepartmentResource::class)
        ->and($resources)->toContain(TagResource::class)
        ->and($resources)->toContain(SlaPolicyResource::class)
        ->and($resources)->toContain(EscalationRuleResource::class)
        ->and($resources)->toContain(CannedResponseResource::class)
        ->and($resources)->toContain(MacroResource::class);
});

it('registers pages with the panel', function () {
    $panel = Filament::getDefaultPanel();
    $pages = $panel->getPages();

    expect($pages)->toContain(Dashboard::class)
        ->and($pages)->toContain(Reports::class)
        ->and($pages)->toContain(Settings::class);
});

it('does not register widgets on the panel (they are page-specific)', function () {
    $panel = Filament::getDefaultPanel();
    $widgets = $panel->getWidgets();

    expect($widgets)->not->toContain(TicketStatsOverview::class)
        ->and($widgets)->not->toContain(TicketsByStatusChart::class)
        ->and($widgets)->not->toContain(TicketsByPriorityChart::class)
        ->and($widgets)->not->toContain(CsatOverviewWidget::class)
        ->and($widgets)->not->toContain(RecentTicketsWidget::class)
        ->and($widgets)->not->toContain(SlaBreachWidget::class);
});

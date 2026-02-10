<?php

use Escalated\Filament\EscalatedFilamentPlugin;
use Filament\Panel;

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

    expect($plugin)->toBeInstanceOf(\Filament\Contracts\Plugin::class);
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
    $panel = \Filament\Facades\Filament::getDefaultPanel();
    $resources = $panel->getResources();

    expect($resources)->toContain(\Escalated\Filament\Resources\TicketResource::class)
        ->and($resources)->toContain(\Escalated\Filament\Resources\DepartmentResource::class)
        ->and($resources)->toContain(\Escalated\Filament\Resources\TagResource::class)
        ->and($resources)->toContain(\Escalated\Filament\Resources\SlaPolicyResource::class)
        ->and($resources)->toContain(\Escalated\Filament\Resources\EscalationRuleResource::class)
        ->and($resources)->toContain(\Escalated\Filament\Resources\CannedResponseResource::class)
        ->and($resources)->toContain(\Escalated\Filament\Resources\MacroResource::class);
});

it('registers pages with the panel', function () {
    $panel = \Filament\Facades\Filament::getDefaultPanel();
    $pages = $panel->getPages();

    expect($pages)->toContain(\Escalated\Filament\Pages\Dashboard::class)
        ->and($pages)->toContain(\Escalated\Filament\Pages\Reports::class)
        ->and($pages)->toContain(\Escalated\Filament\Pages\Settings::class);
});

it('registers widgets with the panel', function () {
    $panel = \Filament\Facades\Filament::getDefaultPanel();
    $widgets = $panel->getWidgets();

    expect($widgets)->toContain(\Escalated\Filament\Widgets\TicketStatsOverview::class)
        ->and($widgets)->toContain(\Escalated\Filament\Widgets\TicketsByStatusChart::class)
        ->and($widgets)->toContain(\Escalated\Filament\Widgets\TicketsByPriorityChart::class)
        ->and($widgets)->toContain(\Escalated\Filament\Widgets\CsatOverviewWidget::class)
        ->and($widgets)->toContain(\Escalated\Filament\Widgets\RecentTicketsWidget::class)
        ->and($widgets)->toContain(\Escalated\Filament\Widgets\SlaBreachWidget::class);
});

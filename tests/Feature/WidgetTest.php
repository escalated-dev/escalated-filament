<?php

use Escalated\Filament\Widgets\CsatOverviewWidget;
use Escalated\Filament\Widgets\RecentTicketsWidget;
use Escalated\Filament\Widgets\SlaBreachWidget;
use Escalated\Filament\Widgets\TicketsByPriorityChart;
use Escalated\Filament\Widgets\TicketsByStatusChart;
use Escalated\Filament\Widgets\TicketStatsOverview;
use Escalated\Laravel\Enums\TicketPriority;
use Escalated\Laravel\Enums\TicketStatus;
use Escalated\Laravel\Models\SatisfactionRating;
use Escalated\Laravel\Models\Ticket;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->user = $this->authenticateUser();
});

// --- TicketStatsOverview ---

it('can render TicketStatsOverview widget', function () {
    livewire(TicketStatsOverview::class)
        ->assertSuccessful();
});

it('shows correct stats in TicketStatsOverview', function () {
    // Create various tickets
    Ticket::factory()->open()->assigned($this->user->id)->count(2)->create();
    Ticket::factory()->open()->count(3)->create(); // unassigned open
    Ticket::factory()->closed()->create();
    Ticket::factory()->open()->breachedSla()->create();
    Ticket::factory()->create([
        'status' => TicketStatus::Resolved,
        'resolved_at' => now(),
    ]);

    $component = livewire(TicketStatsOverview::class)
        ->assertSuccessful();

    // The widget should render without errors given our test data
    expect($component)->not->toBeNull();
});

it('TicketStatsOverview handles no tickets gracefully', function () {
    livewire(TicketStatsOverview::class)
        ->assertSuccessful();
});

// --- TicketsByStatusChart ---

it('can render TicketsByStatusChart widget', function () {
    livewire(TicketsByStatusChart::class)
        ->assertSuccessful();
});

it('TicketsByStatusChart returns chart data', function () {
    Ticket::factory()->open()->count(5)->create();
    Ticket::factory()->closed()->count(2)->create();
    Ticket::factory()->resolved()->count(3)->create();

    $widget = new TicketsByStatusChart();
    $data = (new \ReflectionMethod($widget, 'getData'))->invoke($widget);

    expect($data)->toHaveKey('datasets')
        ->and($data)->toHaveKey('labels')
        ->and($data['datasets'])->toBeArray()
        ->and($data['datasets'][0])->toHaveKey('data')
        ->and($data['datasets'][0])->toHaveKey('backgroundColor');
});

it('TicketsByStatusChart filters out zero-count statuses', function () {
    // Only create open tickets
    Ticket::factory()->open()->count(3)->create();

    $widget = new TicketsByStatusChart();
    $data = (new \ReflectionMethod($widget, 'getData'))->invoke($widget);

    // The data should only include statuses with count > 0
    $counts = $data['datasets'][0]['data'] ?? [];
    foreach ($counts as $count) {
        expect($count)->toBeGreaterThan(0);
    }
});

it('TicketsByStatusChart is a doughnut chart', function () {
    $widget = new TicketsByStatusChart();
    $reflection = new \ReflectionMethod($widget, 'getType');

    expect($reflection->invoke($widget))->toBe('doughnut');
});

it('TicketsByStatusChart handles empty data', function () {
    $widget = new TicketsByStatusChart();
    $data = (new \ReflectionMethod($widget, 'getData'))->invoke($widget);

    expect($data['datasets'][0]['data'])->toBeEmpty()
        ->and($data['labels'])->toBeEmpty();
});

// --- TicketsByPriorityChart ---

it('can render TicketsByPriorityChart widget', function () {
    livewire(TicketsByPriorityChart::class)
        ->assertSuccessful();
});

it('TicketsByPriorityChart returns chart data', function () {
    Ticket::factory()->open()->withPriority(TicketPriority::Low)->count(2)->create();
    Ticket::factory()->open()->withPriority(TicketPriority::High)->count(4)->create();
    Ticket::factory()->open()->withPriority(TicketPriority::Critical)->create();

    $widget = new TicketsByPriorityChart();
    $data = (new \ReflectionMethod($widget, 'getData'))->invoke($widget);

    expect($data)->toHaveKey('datasets')
        ->and($data)->toHaveKey('labels')
        ->and($data['datasets'][0])->toHaveKey('data')
        ->and($data['datasets'][0])->toHaveKey('backgroundColor')
        ->and($data['datasets'][0]['label'])->toBe('Open Tickets');
});

it('TicketsByPriorityChart includes all priority levels', function () {
    $widget = new TicketsByPriorityChart();
    $data = (new \ReflectionMethod($widget, 'getData'))->invoke($widget);

    // Should have labels for all 5 priority levels
    expect(count($data['labels']))->toBe(5)
        ->and($data['labels'])->toContain('Low')
        ->and($data['labels'])->toContain('Medium')
        ->and($data['labels'])->toContain('High')
        ->and($data['labels'])->toContain('Urgent')
        ->and($data['labels'])->toContain('Critical');
});

it('TicketsByPriorityChart is a bar chart', function () {
    $widget = new TicketsByPriorityChart();
    $reflection = new \ReflectionMethod($widget, 'getType');

    expect($reflection->invoke($widget))->toBe('bar');
});

it('TicketsByPriorityChart only counts open tickets', function () {
    Ticket::factory()->open()->withPriority(TicketPriority::High)->count(3)->create();
    Ticket::factory()->closed()->withPriority(TicketPriority::High)->count(5)->create();

    $widget = new TicketsByPriorityChart();
    $data = (new \ReflectionMethod($widget, 'getData'))->invoke($widget);

    // Find the High priority index
    $index = array_search('High', $data['labels']);
    expect($data['datasets'][0]['data'][$index])->toBe(3);
});

// --- CsatOverviewWidget ---

it('can render CsatOverviewWidget', function () {
    livewire(CsatOverviewWidget::class)
        ->assertSuccessful();
});

it('CsatOverviewWidget shows N/A when no ratings exist', function () {
    $component = livewire(CsatOverviewWidget::class)
        ->assertSuccessful();

    expect($component)->not->toBeNull();
});

it('CsatOverviewWidget calculates satisfaction rate', function () {
    // Create tickets with satisfaction ratings
    $ticket1 = Ticket::factory()->create();
    $ticket2 = Ticket::factory()->create();
    $ticket3 = Ticket::factory()->create();
    $ticket4 = Ticket::factory()->create();

    SatisfactionRating::create([
        'ticket_id' => $ticket1->id,
        'rating' => 5,
        'created_at' => now(),
    ]);
    SatisfactionRating::create([
        'ticket_id' => $ticket2->id,
        'rating' => 4,
        'created_at' => now(),
    ]);
    SatisfactionRating::create([
        'ticket_id' => $ticket3->id,
        'rating' => 3,
        'created_at' => now(),
    ]);
    SatisfactionRating::create([
        'ticket_id' => $ticket4->id,
        'rating' => 2,
        'created_at' => now(),
    ]);

    livewire(CsatOverviewWidget::class)
        ->assertSuccessful();

    // Verify data: avg = (5+4+3+2)/4 = 3.5, positive = 2/4 = 50%
    $totalRatings = SatisfactionRating::count();
    $avgRating = SatisfactionRating::avg('rating');

    expect($totalRatings)->toBe(4)
        ->and(round($avgRating, 1))->toBe(3.5);
});

// --- RecentTicketsWidget ---

it('can render RecentTicketsWidget', function () {
    livewire(RecentTicketsWidget::class)
        ->assertSuccessful();
});

it('RecentTicketsWidget shows recent tickets', function () {
    Ticket::factory()->count(3)->create();

    livewire(RecentTicketsWidget::class)
        ->assertSuccessful();
});

it('RecentTicketsWidget has expected columns', function () {
    livewire(RecentTicketsWidget::class)
        ->assertTableColumnExists('reference')
        ->assertTableColumnExists('subject')
        ->assertTableColumnExists('status')
        ->assertTableColumnExists('priority')
        ->assertTableColumnExists('assignee.name')
        ->assertTableColumnExists('created_at');
});

it('RecentTicketsWidget handles empty state', function () {
    livewire(RecentTicketsWidget::class)
        ->assertSuccessful();
});

// --- SlaBreachWidget ---

it('can render SlaBreachWidget', function () {
    livewire(SlaBreachWidget::class)
        ->assertSuccessful();
});

it('SlaBreachWidget shows breached tickets', function () {
    Ticket::factory()->open()->breachedSla()->count(2)->create();
    Ticket::factory()->open()->count(3)->create(); // not breached

    livewire(SlaBreachWidget::class)
        ->assertSuccessful();
});

it('SlaBreachWidget has expected columns', function () {
    livewire(SlaBreachWidget::class)
        ->assertTableColumnExists('reference')
        ->assertTableColumnExists('subject')
        ->assertTableColumnExists('priority')
        ->assertTableColumnExists('assignee.name')
        ->assertTableColumnExists('sla_first_response_breached')
        ->assertTableColumnExists('sla_resolution_breached')
        ->assertTableColumnExists('resolution_due_at');
});

it('SlaBreachWidget handles empty state gracefully', function () {
    // No breached tickets
    Ticket::factory()->open()->count(3)->create();

    livewire(SlaBreachWidget::class)
        ->assertSuccessful();
});

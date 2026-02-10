<?php

use Escalated\Filament\Resources\TicketResource;
use Escalated\Filament\Resources\TicketResource\Pages\CreateTicket;
use Escalated\Filament\Resources\TicketResource\Pages\ListTickets;
use Escalated\Filament\Resources\TicketResource\Pages\ViewTicket;
use Escalated\Laravel\Enums\TicketPriority;
use Escalated\Laravel\Enums\TicketStatus;
use Escalated\Laravel\Models\Department;
use Escalated\Laravel\Models\Tag;
use Escalated\Laravel\Models\Ticket;
use Filament\Tables;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->user = $this->authenticateUser();
});

// --- List Page ---

it('can render the ticket list page', function () {
    livewire(ListTickets::class)
        ->assertSuccessful();
});

it('can list tickets', function () {
    $tickets = Ticket::factory()->count(3)->create();

    livewire(ListTickets::class)
        ->assertCanSeeTableRecords($tickets);
});

it('has the expected table columns', function () {
    livewire(ListTickets::class)
        ->assertTableColumnExists('reference')
        ->assertTableColumnExists('subject')
        ->assertTableColumnExists('status')
        ->assertTableColumnExists('priority')
        ->assertTableColumnExists('department.name')
        ->assertTableColumnExists('assignee.name')
        ->assertTableColumnExists('created_at')
        ->assertTableColumnExists('updated_at');
});

it('can search tickets by reference', function () {
    $ticket = Ticket::factory()->create(['reference' => 'ESC-99999']);
    $other = Ticket::factory()->create(['reference' => 'ESC-00001']);

    livewire(ListTickets::class)
        ->searchTable('ESC-99999')
        ->assertCanSeeTableRecords([$ticket])
        ->assertCanNotSeeTableRecords([$other]);
});

it('can search tickets by subject', function () {
    $ticket = Ticket::factory()->create(['subject' => 'Unique searchable subject']);
    $other = Ticket::factory()->create(['subject' => 'Something else entirely']);

    livewire(ListTickets::class)
        ->searchTable('Unique searchable subject')
        ->assertCanSeeTableRecords([$ticket])
        ->assertCanNotSeeTableRecords([$other]);
});

it('can sort tickets by created_at', function () {
    $older = Ticket::factory()->create(['created_at' => now()->subDays(2)]);
    $newer = Ticket::factory()->create(['created_at' => now()]);

    livewire(ListTickets::class)
        ->sortTable('created_at')
        ->assertCanSeeTableRecords([$older, $newer], inOrder: true);
});

it('can filter tickets by status', function () {
    $open = Ticket::factory()->create(['status' => TicketStatus::Open]);
    $closed = Ticket::factory()->closed()->create();

    livewire(ListTickets::class)
        ->filterTable('status', [TicketStatus::Open->value])
        ->assertCanSeeTableRecords([$open])
        ->assertCanNotSeeTableRecords([$closed]);
});

it('can filter tickets by priority', function () {
    $high = Ticket::factory()->withPriority(TicketPriority::High)->create();
    $low = Ticket::factory()->withPriority(TicketPriority::Low)->create();

    livewire(ListTickets::class)
        ->filterTable('priority', [TicketPriority::High->value])
        ->assertCanSeeTableRecords([$high])
        ->assertCanNotSeeTableRecords([$low]);
});

it('can filter tickets by department', function () {
    $dept = Department::factory()->create();
    $inDept = Ticket::factory()->create(['department_id' => $dept->id]);
    $noDept = Ticket::factory()->create(['department_id' => null]);

    livewire(ListTickets::class)
        ->filterTable('department_id', $dept->id)
        ->assertCanSeeTableRecords([$inDept])
        ->assertCanNotSeeTableRecords([$noDept]);
});

it('has the list page tabs', function () {
    livewire(ListTickets::class)
        ->assertSuccessful();

    // Verify the tabs are defined by checking the page class
    $page = new ListTickets();
    $tabs = $page->getTabs();

    expect($tabs)->toHaveKeys(['all', 'my_tickets', 'unassigned', 'urgent', 'sla_breaching']);
});

it('displays correct navigation badge count', function () {
    Ticket::factory()->open()->count(3)->create();
    Ticket::factory()->closed()->count(2)->create();

    $badge = TicketResource::getNavigationBadge();

    expect($badge)->toBe('3');
});

it('shows correct badge color based on open ticket count', function () {
    // No open tickets = success
    expect(TicketResource::getNavigationBadgeColor())->toBe('success');

    // Some open tickets = warning
    Ticket::factory()->open()->count(5)->create();
    expect(TicketResource::getNavigationBadgeColor())->toBe('warning');
});

// --- Create Page ---

it('can render the create page', function () {
    livewire(CreateTicket::class)
        ->assertSuccessful();
});

it('has the correct form fields on create page', function () {
    livewire(CreateTicket::class)
        ->assertFormFieldExists('subject')
        ->assertFormFieldExists('description')
        ->assertFormFieldExists('priority')
        ->assertFormFieldExists('department_id')
        ->assertFormFieldExists('assigned_to')
        ->assertFormFieldExists('tags');
});

it('can create a ticket', function () {
    $dept = Department::factory()->create();

    livewire(CreateTicket::class)
        ->fillForm([
            'subject' => 'New test ticket',
            'description' => 'This is a test ticket description',
            'priority' => TicketPriority::High->value,
            'department_id' => $dept->id,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('escalated_tickets', [
        'subject' => 'New test ticket',
        'priority' => TicketPriority::High->value,
        'department_id' => $dept->id,
        'status' => 'open',
        'channel' => 'admin',
    ]);
});

it('requires a subject when creating', function () {
    livewire(CreateTicket::class)
        ->fillForm([
            'subject' => '',
            'description' => 'Some description',
            'priority' => TicketPriority::Medium->value,
        ])
        ->call('create')
        ->assertHasFormErrors(['subject' => 'required']);
});

it('requires a description when creating', function () {
    livewire(CreateTicket::class)
        ->fillForm([
            'subject' => 'Test subject',
            'description' => '',
            'priority' => TicketPriority::Medium->value,
        ])
        ->call('create')
        ->assertHasFormErrors(['description' => 'required']);
});

it('requires a priority when creating', function () {
    livewire(CreateTicket::class)
        ->fillForm([
            'subject' => 'Test subject',
            'description' => 'Some description',
            'priority' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['priority' => 'required']);
});

it('generates a reference when creating', function () {
    livewire(CreateTicket::class)
        ->fillForm([
            'subject' => 'Reference generation test',
            'description' => 'Testing that references are auto-generated',
            'priority' => TicketPriority::Medium->value,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $ticket = Ticket::where('subject', 'Reference generation test')->first();
    expect($ticket->reference)->toStartWith('ESC-');
});

it('sets current user as requester on create', function () {
    livewire(CreateTicket::class)
        ->fillForm([
            'subject' => 'Requester test',
            'description' => 'Testing requester assignment',
            'priority' => TicketPriority::Medium->value,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $ticket = Ticket::where('subject', 'Requester test')->first();
    expect($ticket->requester_id)->toBe($this->user->id);
});

// --- View Page ---

it('can render the view page', function () {
    $ticket = Ticket::factory()->create();

    livewire(ViewTicket::class, ['record' => $ticket->getRouteKey()])
        ->assertSuccessful();
});

it('displays ticket information on view page', function () {
    $ticket = Ticket::factory()->create([
        'subject' => 'Viewable ticket subject',
        'status' => TicketStatus::Open,
        'priority' => TicketPriority::High,
    ]);

    livewire(ViewTicket::class, ['record' => $ticket->getRouteKey()])
        ->assertSuccessful();
});

// --- Resource Configuration ---

it('uses Ticket as the model', function () {
    expect(TicketResource::getModel())->toBe(Ticket::class);
});

it('has navigation group from plugin', function () {
    expect(TicketResource::getNavigationGroup())->toBe('Support');
});

it('has correct record title attribute', function () {
    $reflection = new \ReflectionClass(TicketResource::class);
    $property = $reflection->getProperty('recordTitleAttribute');

    expect($property->getDefaultValue())->toBe('subject');
});

it('registers relation managers', function () {
    $relations = TicketResource::getRelations();

    expect($relations)->toContain(\Escalated\Filament\Resources\TicketResource\RelationManagers\RepliesRelationManager::class)
        ->and($relations)->toContain(\Escalated\Filament\Resources\TicketResource\RelationManagers\ActivitiesRelationManager::class)
        ->and($relations)->toContain(\Escalated\Filament\Resources\TicketResource\RelationManagers\FollowersRelationManager::class);
});

it('has index, create, and view pages', function () {
    $pages = TicketResource::getPages();

    expect($pages)->toHaveKeys(['index', 'create', 'view']);
});

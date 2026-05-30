<?php

use Escalated\Filament\Resources\TicketResource\Pages\ViewTicket;
use Escalated\Filament\Resources\TicketResource\RelationManagers\SubjectsRelationManager;
use Escalated\Filament\Support\TicketSubjectTypeResolver;
use Escalated\Filament\Tests\Models\FakeProject;
use Escalated\Laravel\Models\Ticket;
use Illuminate\Validation\ValidationException;

use function Pest\Livewire\livewire;

beforeEach(function () {
    if (! TicketSubjectTypeResolver::isAvailable()) {
        $this->markTestSkipped('Requires escalated-laravel#122 (TicketSubjectLink / attachSubject).');
    }

    config(['escalated.ticket_subjects.types' => [FakeProject::class]]);

    $this->authenticateUser();
});

it('can render the subjects relation manager', function () {
    $ticket = Ticket::factory()->create();

    livewire(SubjectsRelationManager::class, [
        'ownerRecord' => $ticket,
        'pageClass' => ViewTicket::class,
    ])->assertSuccessful();
});

it('attaches and detaches subjects through ticket model helpers', function () {
    $ticket = Ticket::factory()->create();
    $project = FakeProject::create([
        'id' => 'prj-1',
        'name' => 'Acme Redesign',
        'account' => 'Acme Corp',
    ]);

    $ticket->attachSubject($project, 'project');

    expect($ticket->subjects()->count())->toBe(1)
        ->and($ticket->subjects()->first()->role)->toBe('project');

    $ticket->detachSubject($project);

    expect($ticket->fresh()->subjects()->count())->toBe(0);
});

it('rejects disallowed subject types via the resolver', function () {
    expect(fn () => TicketSubjectTypeResolver::resolveModelClass('App\\Models\\Evil'))
        ->toThrow(ValidationException::class);
});

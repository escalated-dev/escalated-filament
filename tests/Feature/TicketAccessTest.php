<?php

use Escalated\Filament\Livewire\TicketConversation;
use Escalated\Filament\Pages\Reports;
use Escalated\Filament\Resources\SlaPolicyResource\Pages\CreateSlaPolicy;
use Escalated\Filament\Resources\TicketResource\Pages\CreateTicket;
use Escalated\Filament\Resources\TicketResource\Pages\ListTickets;
use Escalated\Filament\Resources\TicketResource\RelationManagers\RepliesRelationManager;
use Escalated\Filament\Support\TicketAccess;
use Escalated\Filament\Tests\Models\FakeProject;
use Escalated\Filament\Tests\User;
use Escalated\Filament\Widgets\RecentTicketsWidget;
use Escalated\Filament\Widgets\TicketsByStatusChart;
use Escalated\Laravel\Contracts\TicketAccountResolver;
use Escalated\Laravel\Models\Department;
use Escalated\Laravel\Models\Reply;
use Escalated\Laravel\Models\SlaPolicy;
use Escalated\Laravel\Models\Ticket;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

use function Pest\Livewire\livewire;

beforeEach(function () {
    Notification::fake();
    $this->agent = $this->authenticateUser();
    Gate::define('escalated-admin', fn () => false);
    $this->department = Department::factory()->create();
    $this->department->agents()->attach($this->agent);
    $this->visible = Ticket::factory()->create(['department_id' => $this->department->id, 'subject' => 'Shared marker']);
    $this->hidden = Ticket::factory()->create(['department_id' => Department::factory()->create()->id, 'subject' => 'Shared marker']);
    $this->note = Reply::factory()->create(['ticket_id' => $this->hidden->id, 'is_internal_note' => true, 'is_pinned' => true]);
    config(['escalated.visibility' => 'department']);
});

it('scopes list search widgets and report aggregates', function () {
    livewire(ListTickets::class)->searchTable('Shared marker')
        ->assertCanSeeTableRecords([$this->visible])->assertCanNotSeeTableRecords([$this->hidden]);
    livewire(RecentTicketsWidget::class)->assertCanSeeTableRecords([$this->visible])->assertCanNotSeeTableRecords([$this->hidden]);
    $data = (new ReflectionMethod(TicketsByStatusChart::class, 'getData'))->invoke(new TicketsByStatusChart);
    expect(array_sum($data['datasets'][0]['data']))->toBe(1);
    $reports = new Reports;
    $reports->mount();
    expect($reports->getStats()['total_tickets'])->toBe(1);
});

it('denies direct conversation child reads for hidden tickets', function (string $method) {
    $component = new TicketConversation;
    $component->ticketId = $this->hidden->id;
    expect(fn () => $component->$method())->toThrow(ModelNotFoundException::class);
})->with(['getRepliesProperty', 'getPinnedNotesProperty']);

it('denies conversation access after the staff seat is revoked', function () {
    $this->visible->updateQuietly(['requester_type' => $this->agent->getMorphClass(), 'requester_id' => $this->agent->id]);
    Gate::define('escalated-agent', fn () => false);
    $component = new TicketConversation;
    $component->ticketId = $this->visible->id;
    expect(fn () => $component->getRepliesProperty())->toThrow(HttpException::class);
});

it('denies pin changes after department access is revoked', function () {
    $component = new TicketConversation;
    $component->ticketId = $this->hidden->id;
    expect(fn () => $component->togglePin($this->note->id))->toThrow(ModelNotFoundException::class);
    expect($this->note->fresh()->is_pinned)->toBeTrue();
});

it('rechecks relation owners after department membership changes', function () {
    $manager = new RepliesRelationManager;
    $manager->ownerRecord = $this->visible;
    expect($manager->getOwnerRecord()->id)->toBe($this->visible->id);
    $this->department->agents()->detach($this->agent);
    expect(fn () => $manager->getOwnerRecord())->toThrow(ModelNotFoundException::class);
});

it('keeps hidden tickets out of bulk actions', function () {
    livewire(ListTickets::class)->callTableBulkAction('changeStatusBulk', [$this->visible, $this->hidden], ['status' => 'closed']);
    expect($this->visible->fresh()->status->value)->toBe('closed')
        ->and($this->hidden->fresh()->status->value)->toBe('open');
});

it('filters assignment candidates and rejects a forged creation assignee', function () {
    $other = User::create(['name' => 'Other agent', 'email' => 'other@example.test', 'password' => bcrypt('password')]);
    expect(TicketAccess::assignmentOptions($this->visible))->toHaveKey($this->agent->id)->not->toHaveKey($other->id);
    $page = livewire(CreateTicket::class)->instance();
    $method = new ReflectionMethod(CreateTicket::class, 'mutateFormDataBeforeCreate');
    $method->setAccessible(true);
    expect(fn () => $method->invoke($page, ['subject' => 'Forged assignment', 'description' => 'Details', 'priority' => 'medium', 'department_id' => $this->department->id, 'assigned_to' => $other->id]))
        ->toThrow(ValidationException::class);
    expect(Ticket::where('subject', 'Forged assignment')->exists())->toBeFalse();
});

it('creates type targeted tickets with requester account resolution', function () {
    $account = FakeProject::create(['id' => 'account-001', 'name' => 'Account']);
    app()->instance(TicketAccountResolver::class, new class($account) implements TicketAccountResolver
    {
        public function __construct(private Model $account) {}

        public function resolve(Model $model): ?Model
        {
            return $this->account;
        }
    });
    config(['escalated.accounts.enabled' => true, 'escalated.accounts.scope_agents' => true, 'escalated.sla.targeted' => true]);
    $policy = SlaPolicy::factory()->create(['department_id' => $this->department->id, 'ticket_type' => 'incident']);
    livewire(CreateTicket::class)
        ->fillForm(['subject' => 'Routed incident', 'description' => 'Details', 'priority' => 'medium', 'department_id' => $this->department->id, 'ticket_type' => 'incident'])
        ->call('create')->assertHasNoFormErrors();
    $ticket = Ticket::where('subject', 'Routed incident')->firstOrFail();
    expect($ticket->account_id)->toBe((string) $account->id)->and($ticket->sla_policy_id)->toBe($policy->id);
});

it('persists optional SLA targets from the admin form', function () {
    Gate::define('escalated-admin', fn () => true);
    config(['escalated.sla.targeted' => true]);
    livewire(CreateSlaPolicy::class)
        ->fillForm(['name' => 'Incident target', 'department_id' => $this->department->id, 'ticket_type' => 'incident'])
        ->call('create')->assertHasNoFormErrors();
    $policy = SlaPolicy::where('name', 'Incident target')->firstOrFail();
    expect($policy->department_id)->toBe($this->department->id)->and($policy->ticket_type)->toBe('incident');
});

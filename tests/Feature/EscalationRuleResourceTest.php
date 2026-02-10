<?php

use Escalated\Filament\Resources\EscalationRuleResource;
use Escalated\Filament\Resources\EscalationRuleResource\Pages\CreateEscalationRule;
use Escalated\Filament\Resources\EscalationRuleResource\Pages\EditEscalationRule;
use Escalated\Filament\Resources\EscalationRuleResource\Pages\ListEscalationRules;
use Escalated\Laravel\Models\EscalationRule;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->user = $this->authenticateUser();
});

// --- List Page ---

it('can render the escalation rule list page', function () {
    livewire(ListEscalationRules::class)
        ->assertSuccessful();
});

it('can list escalation rules', function () {
    $rules = EscalationRule::factory()->count(3)->create();

    livewire(ListEscalationRules::class)
        ->assertCanSeeTableRecords($rules);
});

it('has the expected table columns', function () {
    livewire(ListEscalationRules::class)
        ->assertTableColumnExists('order')
        ->assertTableColumnExists('name')
        ->assertTableColumnExists('description')
        ->assertTableColumnExists('trigger_type')
        ->assertTableColumnExists('is_active')
        ->assertTableColumnExists('created_at');
});

it('can search escalation rules by name', function () {
    $rule = EscalationRule::factory()->create(['name' => 'Urgent Escalation']);
    $other = EscalationRule::factory()->create(['name' => 'Low Priority Rule']);

    livewire(ListEscalationRules::class)
        ->searchTable('Urgent')
        ->assertCanSeeTableRecords([$rule])
        ->assertCanNotSeeTableRecords([$other]);
});

it('can filter escalation rules by active status', function () {
    $active = EscalationRule::factory()->create(['is_active' => true]);
    $inactive = EscalationRule::factory()->create(['is_active' => false]);

    livewire(ListEscalationRules::class)
        ->filterTable('is_active', true)
        ->assertCanSeeTableRecords([$active])
        ->assertCanNotSeeTableRecords([$inactive]);
});

// --- Create Page ---

it('can render the create page', function () {
    livewire(CreateEscalationRule::class)
        ->assertSuccessful();
});

it('has the correct form fields', function () {
    livewire(CreateEscalationRule::class)
        ->assertFormFieldExists('name')
        ->assertFormFieldExists('description')
        ->assertFormFieldExists('trigger_type')
        ->assertFormFieldExists('order')
        ->assertFormFieldExists('is_active')
        ->assertFormFieldExists('conditions')
        ->assertFormFieldExists('actions');
});

it('can create an escalation rule', function () {
    livewire(CreateEscalationRule::class)
        ->fillForm([
            'name' => 'High Priority Auto-Escalate',
            'description' => 'Escalate high priority tickets after 2 hours',
            'trigger_type' => 'automatic',
            'order' => 1,
            'is_active' => true,
            'conditions' => [
                ['field' => 'priority', 'value' => 'high'],
            ],
            'actions' => [
                ['type' => 'escalate'],
            ],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('escalated_escalation_rules', [
        'name' => 'High Priority Auto-Escalate',
        'trigger_type' => 'automatic',
        'is_active' => true,
    ]);
});

it('requires a name when creating', function () {
    livewire(CreateEscalationRule::class)
        ->fillForm([
            'name' => '',
            'trigger_type' => 'automatic',
        ])
        ->call('create')
        ->assertHasFormErrors(['name' => 'required']);
});

// --- Edit Page ---

it('can render the edit page', function () {
    $rule = EscalationRule::factory()->create();

    livewire(EditEscalationRule::class, ['record' => $rule->id])
        ->assertSuccessful();
});

it('can update an escalation rule', function () {
    $rule = EscalationRule::factory()->create(['name' => 'Old Rule']);

    livewire(EditEscalationRule::class, ['record' => $rule->id])
        ->fillForm([
            'name' => 'Updated Rule',
            'is_active' => false,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $rule->refresh();
    expect($rule->name)->toBe('Updated Rule')
        ->and($rule->is_active)->toBeFalse();
});

// --- Resource Configuration ---

it('uses EscalationRule as the model', function () {
    expect(EscalationRuleResource::getModel())->toBe(EscalationRule::class);
});

it('has navigation group from plugin', function () {
    expect(EscalationRuleResource::getNavigationGroup())->toBe('Support');
});

it('has index, create, and edit pages', function () {
    $pages = EscalationRuleResource::getPages();

    expect($pages)->toHaveKeys(['index', 'create', 'edit']);
});

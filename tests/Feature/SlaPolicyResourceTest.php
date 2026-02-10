<?php

use Escalated\Filament\Resources\SlaPolicyResource;
use Escalated\Filament\Resources\SlaPolicyResource\Pages\CreateSlaPolicy;
use Escalated\Filament\Resources\SlaPolicyResource\Pages\EditSlaPolicy;
use Escalated\Filament\Resources\SlaPolicyResource\Pages\ListSlaPolicies;
use Escalated\Laravel\Models\SlaPolicy;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->user = $this->authenticateUser();
});

// --- List Page ---

it('can render the SLA policy list page', function () {
    livewire(ListSlaPolicies::class)
        ->assertSuccessful();
});

it('can list SLA policies', function () {
    $policies = SlaPolicy::factory()->count(3)->create();

    livewire(ListSlaPolicies::class)
        ->assertCanSeeTableRecords($policies);
});

it('has the expected table columns', function () {
    livewire(ListSlaPolicies::class)
        ->assertTableColumnExists('name')
        ->assertTableColumnExists('description')
        ->assertTableColumnExists('is_default')
        ->assertTableColumnExists('business_hours_only')
        ->assertTableColumnExists('is_active')
        ->assertTableColumnExists('created_at');
});

it('can search SLA policies by name', function () {
    $policy = SlaPolicy::factory()->create(['name' => 'Premium SLA']);
    $other = SlaPolicy::factory()->create(['name' => 'Basic SLA']);

    livewire(ListSlaPolicies::class)
        ->searchTable('Premium')
        ->assertCanSeeTableRecords([$policy])
        ->assertCanNotSeeTableRecords([$other]);
});

it('can filter SLA policies by active status', function () {
    $active = SlaPolicy::factory()->create(['is_active' => true]);
    $inactive = SlaPolicy::factory()->create(['is_active' => false]);

    livewire(ListSlaPolicies::class)
        ->filterTable('is_active', true)
        ->assertCanSeeTableRecords([$active])
        ->assertCanNotSeeTableRecords([$inactive]);
});

it('can filter SLA policies by default status', function () {
    $default = SlaPolicy::factory()->default()->create();
    $nonDefault = SlaPolicy::factory()->create(['is_default' => false]);

    livewire(ListSlaPolicies::class)
        ->filterTable('is_default', true)
        ->assertCanSeeTableRecords([$default])
        ->assertCanNotSeeTableRecords([$nonDefault]);
});

// --- Create Page ---

it('can render the create page', function () {
    livewire(CreateSlaPolicy::class)
        ->assertSuccessful();
});

it('has the correct form fields', function () {
    livewire(CreateSlaPolicy::class)
        ->assertFormFieldExists('name')
        ->assertFormFieldExists('description')
        ->assertFormFieldExists('is_default')
        ->assertFormFieldExists('business_hours_only')
        ->assertFormFieldExists('is_active');
});

it('has priority-based first response hour fields', function () {
    livewire(CreateSlaPolicy::class)
        ->assertFormFieldExists('first_response_hours.low')
        ->assertFormFieldExists('first_response_hours.medium')
        ->assertFormFieldExists('first_response_hours.high')
        ->assertFormFieldExists('first_response_hours.urgent')
        ->assertFormFieldExists('first_response_hours.critical');
});

it('has priority-based resolution hour fields', function () {
    livewire(CreateSlaPolicy::class)
        ->assertFormFieldExists('resolution_hours.low')
        ->assertFormFieldExists('resolution_hours.medium')
        ->assertFormFieldExists('resolution_hours.high')
        ->assertFormFieldExists('resolution_hours.urgent')
        ->assertFormFieldExists('resolution_hours.critical');
});

it('can create an SLA policy', function () {
    livewire(CreateSlaPolicy::class)
        ->fillForm([
            'name' => 'Standard SLA',
            'description' => 'Standard response times',
            'is_default' => false,
            'business_hours_only' => false,
            'is_active' => true,
            'first_response_hours.low' => 24,
            'first_response_hours.medium' => 8,
            'first_response_hours.high' => 4,
            'first_response_hours.urgent' => 2,
            'first_response_hours.critical' => 1,
            'resolution_hours.low' => 72,
            'resolution_hours.medium' => 48,
            'resolution_hours.high' => 24,
            'resolution_hours.urgent' => 8,
            'resolution_hours.critical' => 4,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('escalated_sla_policies', [
        'name' => 'Standard SLA',
        'is_active' => true,
    ]);
});

it('requires a name when creating SLA policy', function () {
    livewire(CreateSlaPolicy::class)
        ->fillForm([
            'name' => '',
        ])
        ->call('create')
        ->assertHasFormErrors(['name' => 'required']);
});

// --- Edit Page ---

it('can render the edit page', function () {
    $policy = SlaPolicy::factory()->create();

    livewire(EditSlaPolicy::class, ['record' => $policy->id])
        ->assertSuccessful();
});

it('can fill the edit form with existing data', function () {
    $policy = SlaPolicy::factory()->create([
        'name' => 'Enterprise SLA',
        'is_default' => true,
        'business_hours_only' => true,
        'is_active' => true,
    ]);

    livewire(EditSlaPolicy::class, ['record' => $policy->id])
        ->assertFormSet([
            'name' => 'Enterprise SLA',
            'is_default' => true,
            'business_hours_only' => true,
            'is_active' => true,
        ]);
});

it('can update an SLA policy', function () {
    $policy = SlaPolicy::factory()->create(['name' => 'Old SLA']);

    livewire(EditSlaPolicy::class, ['record' => $policy->id])
        ->fillForm([
            'name' => 'Updated SLA',
            'is_default' => true,
            'is_active' => true,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $policy->refresh();
    expect($policy->name)->toBe('Updated SLA')
        ->and($policy->is_default)->toBeTrue();
});

// --- Resource Configuration ---

it('uses SlaPolicy as the model', function () {
    expect(SlaPolicyResource::getModel())->toBe(SlaPolicy::class);
});

it('has navigation group from plugin', function () {
    expect(SlaPolicyResource::getNavigationGroup())->toBe('Support');
});

it('has correct model label', function () {
    expect(SlaPolicyResource::getModelLabel())->toBe('SLA Policy');
    expect(SlaPolicyResource::getPluralModelLabel())->toBe('SLA Policies');
});

it('has index, create, and edit pages', function () {
    $pages = SlaPolicyResource::getPages();

    expect($pages)->toHaveKeys(['index', 'create', 'edit']);
});

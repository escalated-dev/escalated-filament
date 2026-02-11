<?php

use Escalated\Filament\Resources\MacroResource;
use Escalated\Filament\Resources\MacroResource\Pages\CreateMacro;
use Escalated\Filament\Resources\MacroResource\Pages\EditMacro;
use Escalated\Filament\Resources\MacroResource\Pages\ListMacros;
use Escalated\Laravel\Models\Macro;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->user = $this->authenticateUser();
});

// --- List Page ---

it('can render the macro list page', function () {
    livewire(ListMacros::class)
        ->assertSuccessful();
});

it('can list macros', function () {
    $macros = collect([
        Macro::create([
            'name' => 'Macro 1',
            'actions' => [['type' => 'status', 'value' => 'resolved']],
            'created_by' => $this->user->id,
            'is_shared' => true,
            'order' => 0,
        ]),
        Macro::create([
            'name' => 'Macro 2',
            'actions' => [['type' => 'priority', 'value' => 'high']],
            'created_by' => $this->user->id,
            'is_shared' => true,
            'order' => 1,
        ]),
    ]);

    livewire(ListMacros::class)
        ->assertCanSeeTableRecords($macros);
});

it('has the expected table columns', function () {
    livewire(ListMacros::class)
        ->assertTableColumnExists('order')
        ->assertTableColumnExists('name')
        ->assertTableColumnExists('description')
        ->assertTableColumnExists('is_shared')
        ->assertTableColumnExists('created_at');
});

it('can search macros by name', function () {
    $macro = Macro::create([
        'name' => 'Quick Close',
        'actions' => [['type' => 'status', 'value' => 'closed']],
        'created_by' => $this->user->id,
        'is_shared' => true,
        'order' => 0,
    ]);
    $other = Macro::create([
        'name' => 'Escalate to Manager',
        'actions' => [['type' => 'escalate']],
        'created_by' => $this->user->id,
        'is_shared' => true,
        'order' => 1,
    ]);

    livewire(ListMacros::class)
        ->searchTable('Quick Close')
        ->assertCanSeeTableRecords([$macro])
        ->assertCanNotSeeTableRecords([$other]);
});

it('can filter macros by shared status', function () {
    $shared = Macro::create([
        'name' => 'Shared Macro',
        'actions' => [],
        'created_by' => $this->user->id,
        'is_shared' => true,
        'order' => 0,
    ]);
    $personal = Macro::create([
        'name' => 'Personal Macro',
        'actions' => [],
        'created_by' => $this->user->id,
        'is_shared' => false,
        'order' => 1,
    ]);

    livewire(ListMacros::class)
        ->filterTable('is_shared', true)
        ->assertCanSeeTableRecords([$shared])
        ->assertCanNotSeeTableRecords([$personal]);
});

// --- Create Page ---

it('can render the create page', function () {
    livewire(CreateMacro::class)
        ->assertSuccessful();
});

it('has the correct form fields', function () {
    livewire(CreateMacro::class)
        ->assertFormFieldExists('name')
        ->assertFormFieldExists('description')
        ->assertFormFieldExists('order')
        ->assertFormFieldExists('is_shared')
        ->assertFormFieldExists('actions');
});

it('can create a macro', function () {
    // Note: Repeater fields with live() selects can't be reliably filled via
    // fillForm() in tests because Livewire round-trips don't fire, so the
    // dependent value field's options remain empty. We test creation with
    // an empty actions array; repeater data is validated via list/edit tests.
    livewire(CreateMacro::class)
        ->fillForm([
            'name' => 'Auto-Resolve',
            'description' => 'Resolve and add a closing note',
            'is_shared' => true,
            'order' => 1,
            'actions' => [],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('escalated_macros', [
        'name' => 'Auto-Resolve',
        'is_shared' => true,
    ]);
});

it('requires a name when creating macro', function () {
    livewire(CreateMacro::class)
        ->fillForm([
            'name' => '',
        ])
        ->call('create')
        ->assertHasFormErrors(['name' => 'required']);
});

// --- Edit Page ---

it('can render the edit page', function () {
    $macro = Macro::create([
        'name' => 'Test Macro',
        'actions' => [['type' => 'status', 'value' => 'resolved']],
        'created_by' => $this->user->id,
        'is_shared' => true,
        'order' => 0,
    ]);

    livewire(EditMacro::class, ['record' => $macro->id])
        ->assertSuccessful();
});

it('can update a macro', function () {
    $macro = Macro::create([
        'name' => 'Old Macro Name',
        'actions' => [],
        'created_by' => $this->user->id,
        'is_shared' => true,
        'order' => 0,
    ]);

    livewire(EditMacro::class, ['record' => $macro->id])
        ->fillForm([
            'name' => 'New Macro Name',
            'is_shared' => false,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $macro->refresh();
    expect($macro->name)->toBe('New Macro Name')
        ->and($macro->is_shared)->toBeFalse();
});

// --- Resource Configuration ---

it('uses Macro as the model', function () {
    expect(MacroResource::getModel())->toBe(Macro::class);
});

it('has navigation group from plugin', function () {
    expect(MacroResource::getNavigationGroup())->toBe('Support');
});

it('has index, create, and edit pages', function () {
    $pages = MacroResource::getPages();

    expect($pages)->toHaveKeys(['index', 'create', 'edit']);
});

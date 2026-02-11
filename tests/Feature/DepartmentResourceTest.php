<?php

use Escalated\Filament\Resources\DepartmentResource;
use Escalated\Filament\Resources\DepartmentResource\Pages\CreateDepartment;
use Escalated\Filament\Resources\DepartmentResource\Pages\EditDepartment;
use Escalated\Filament\Resources\DepartmentResource\Pages\ListDepartments;
use Escalated\Laravel\Models\Department;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->user = $this->authenticateUser();
});

// --- List Page ---

it('can render the department list page', function () {
    livewire(ListDepartments::class)
        ->assertSuccessful();
});

it('can list departments', function () {
    $departments = Department::factory()->count(3)->create();

    livewire(ListDepartments::class)
        ->assertCanSeeTableRecords($departments);
});

it('has the expected table columns', function () {
    livewire(ListDepartments::class)
        ->assertTableColumnExists('name')
        ->assertTableColumnExists('slug')
        ->assertTableColumnExists('description')
        ->assertTableColumnExists('is_active');
});

it('can search departments by name', function () {
    $dept = Department::create([
        'name' => 'Engineering',
        'slug' => 'engineering',
        'is_active' => true,
    ]);
    $other = Department::create([
        'name' => 'Marketing',
        'slug' => 'marketing',
        'is_active' => true,
    ]);

    livewire(ListDepartments::class)
        ->searchTable('Engineering')
        ->assertCanSeeTableRecords([$dept])
        ->assertCanNotSeeTableRecords([$other]);
});

it('can filter departments by active status', function () {
    $active = Department::create([
        'name' => 'Active Dept',
        'slug' => 'active-dept',
        'is_active' => true,
    ]);
    $inactive = Department::create([
        'name' => 'Inactive Dept',
        'slug' => 'inactive-dept',
        'is_active' => false,
    ]);

    livewire(ListDepartments::class)
        ->filterTable('is_active', true)
        ->assertCanSeeTableRecords([$active])
        ->assertCanNotSeeTableRecords([$inactive]);
});

// --- Create Page ---

it('can render the create page', function () {
    livewire(CreateDepartment::class)
        ->assertSuccessful();
});

it('has the correct form fields on create page', function () {
    livewire(CreateDepartment::class)
        ->assertFormFieldExists('name')
        ->assertFormFieldExists('slug')
        ->assertFormFieldExists('description')
        ->assertFormFieldExists('is_active');
});

it('can create a department', function () {
    livewire(CreateDepartment::class)
        ->fillForm([
            'name' => 'New Department',
            'slug' => 'new-department',
            'description' => 'Test description',
            'is_active' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('escalated_departments', [
        'name' => 'New Department',
        'slug' => 'new-department',
    ]);
});

it('requires name when creating department', function () {
    livewire(CreateDepartment::class)
        ->fillForm([
            'name' => '',
            'slug' => 'test-slug',
        ])
        ->call('create')
        ->assertHasFormErrors(['name' => 'required']);
});

it('requires slug when creating department', function () {
    livewire(CreateDepartment::class)
        ->fillForm([
            'name' => 'Test Name',
            'slug' => '',
        ])
        ->call('create')
        ->assertHasFormErrors(['slug' => 'required']);
});

it('requires unique slug when creating department', function () {
    Department::create([
        'name' => 'Existing',
        'slug' => 'existing-slug',
        'is_active' => true,
    ]);

    livewire(CreateDepartment::class)
        ->fillForm([
            'name' => 'Another',
            'slug' => 'existing-slug',
        ])
        ->call('create')
        ->assertHasFormErrors(['slug' => 'unique']);
});

// --- Edit Page ---

it('can render the edit page', function () {
    $dept = Department::create([
        'name' => 'Editable',
        'slug' => 'editable',
        'is_active' => true,
    ]);

    livewire(EditDepartment::class, ['record' => $dept->id])
        ->assertSuccessful();
});

it('can fill the edit form with existing data', function () {
    $dept = Department::create([
        'name' => 'Existing Department',
        'slug' => 'existing-department',
        'description' => 'Old description',
        'is_active' => true,
    ]);

    livewire(EditDepartment::class, ['record' => $dept->id])
        ->assertFormSet([
            'name' => 'Existing Department',
            'slug' => 'existing-department',
            'description' => 'Old description',
            'is_active' => true,
        ]);
});

it('can update a department', function () {
    $dept = Department::create([
        'name' => 'Old Name',
        'slug' => 'old-name',
        'is_active' => true,
    ]);

    livewire(EditDepartment::class, ['record' => $dept->id])
        ->fillForm([
            'name' => 'Updated Name',
            'slug' => 'updated-name',
            'description' => 'Updated description',
            'is_active' => false,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $dept->refresh();
    expect($dept->name)->toBe('Updated Name')
        ->and($dept->slug)->toBe('updated-name')
        ->and($dept->is_active)->toBeFalse();
});

// --- Resource Configuration ---

it('uses Department as the model', function () {
    expect(DepartmentResource::getModel())->toBe(Department::class);
});

it('has navigation group from plugin', function () {
    expect(DepartmentResource::getNavigationGroup())->toBe('Support');
});

it('has navigation label Departments', function () {
    expect(DepartmentResource::getNavigationLabel())->toBe('Departments');
});

it('has index, create, and edit pages', function () {
    $pages = DepartmentResource::getPages();

    expect($pages)->toHaveKeys(['index', 'create', 'edit']);
});

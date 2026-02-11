<?php

use Escalated\Filament\Resources\CannedResponseResource;
use Escalated\Filament\Resources\CannedResponseResource\Pages\CreateCannedResponse;
use Escalated\Filament\Resources\CannedResponseResource\Pages\EditCannedResponse;
use Escalated\Filament\Resources\CannedResponseResource\Pages\ListCannedResponses;
use Escalated\Laravel\Models\CannedResponse;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->user = $this->authenticateUser();
});

// --- List Page ---

it('can render the canned response list page', function () {
    livewire(ListCannedResponses::class)
        ->assertSuccessful();
});

it('can list canned responses', function () {
    $responses = CannedResponse::factory()->count(3)->create([
        'created_by' => $this->user->id,
    ]);

    livewire(ListCannedResponses::class)
        ->assertCanSeeTableRecords($responses);
});

it('has the expected table columns', function () {
    livewire(ListCannedResponses::class)
        ->assertTableColumnExists('title')
        ->assertTableColumnExists('category')
        ->assertTableColumnExists('body')
        ->assertTableColumnExists('is_shared')
        ->assertTableColumnExists('created_at');
});

it('can search canned responses by title', function () {
    $response = CannedResponse::factory()->create([
        'title' => 'Welcome greeting',
        'created_by' => $this->user->id,
    ]);
    $other = CannedResponse::factory()->create([
        'title' => 'Closing farewell',
        'created_by' => $this->user->id,
    ]);

    livewire(ListCannedResponses::class)
        ->searchTable('Welcome')
        ->assertCanSeeTableRecords([$response])
        ->assertCanNotSeeTableRecords([$other]);
});

it('can filter canned responses by shared status', function () {
    $shared = CannedResponse::factory()->create([
        'is_shared' => true,
        'created_by' => $this->user->id,
    ]);
    $personal = CannedResponse::factory()->personal()->create([
        'created_by' => $this->user->id,
    ]);

    livewire(ListCannedResponses::class)
        ->filterTable('is_shared', true)
        ->assertCanSeeTableRecords([$shared])
        ->assertCanNotSeeTableRecords([$personal]);
});

// --- Create Page ---

it('can render the create page', function () {
    livewire(CreateCannedResponse::class)
        ->assertSuccessful();
});

it('has the correct form fields', function () {
    livewire(CreateCannedResponse::class)
        ->assertFormFieldExists('title')
        ->assertFormFieldExists('category')
        ->assertFormFieldExists('body')
        ->assertFormFieldExists('is_shared');
});

it('can create a canned response', function () {
    livewire(CreateCannedResponse::class)
        ->fillForm([
            'title' => 'New Canned Response',
            'body' => '<p>Thank you for contacting us.</p>',
            'category' => 'greeting',
            'is_shared' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('escalated_canned_responses', [
        'title' => 'New Canned Response',
        'category' => 'greeting',
        'is_shared' => true,
    ]);
});

it('requires a title when creating canned response', function () {
    livewire(CreateCannedResponse::class)
        ->fillForm([
            'title' => '',
            'body' => '<p>Some body</p>',
        ])
        ->call('create')
        ->assertHasFormErrors(['title' => 'required']);
});

it('requires body when creating canned response', function () {
    livewire(CreateCannedResponse::class)
        ->fillForm([
            'title' => 'Test',
            'body' => '',
        ])
        ->call('create')
        ->assertHasFormErrors(['body' => 'required']);
});

// --- Edit Page ---

it('can render the edit page', function () {
    $response = CannedResponse::factory()->create([
        'created_by' => $this->user->id,
    ]);

    livewire(EditCannedResponse::class, ['record' => $response->id])
        ->assertSuccessful();
});

it('can fill the edit form with existing data', function () {
    $response = CannedResponse::factory()->create([
        'title' => 'Existing Response',
        'body' => '<p>Body text</p>',
        'category' => 'billing',
        'is_shared' => true,
        'created_by' => $this->user->id,
    ]);

    livewire(EditCannedResponse::class, ['record' => $response->id])
        ->assertFormSet([
            'title' => 'Existing Response',
            'body' => '<p>Body text</p>',
            'category' => 'billing',
            'is_shared' => true,
        ]);
});

it('can update a canned response', function () {
    $response = CannedResponse::factory()->create([
        'title' => 'Old Title',
        'body' => '<p>Old body</p>',
        'created_by' => $this->user->id,
    ]);

    livewire(EditCannedResponse::class, ['record' => $response->id])
        ->fillForm([
            'title' => 'Updated Title',
            'body' => '<p>Updated body</p>',
            'category' => 'troubleshooting',
            'is_shared' => false,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $response->refresh();
    expect($response->title)->toBe('Updated Title')
        ->and($response->category)->toBe('troubleshooting')
        ->and($response->is_shared)->toBeFalse();
});

// --- Resource Configuration ---

it('uses CannedResponse as the model', function () {
    expect(CannedResponseResource::getModel())->toBe(CannedResponse::class);
});

it('has navigation group from plugin', function () {
    expect(CannedResponseResource::getNavigationGroup())->toBe('Support');
});

it('has index, create, and edit pages', function () {
    $pages = CannedResponseResource::getPages();

    expect($pages)->toHaveKeys(['index', 'create', 'edit']);
});

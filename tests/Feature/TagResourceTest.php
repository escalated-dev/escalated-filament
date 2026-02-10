<?php

use Escalated\Filament\Resources\TagResource;
use Escalated\Filament\Resources\TagResource\Pages\CreateTag;
use Escalated\Filament\Resources\TagResource\Pages\EditTag;
use Escalated\Filament\Resources\TagResource\Pages\ListTags;
use Escalated\Laravel\Models\Tag;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->user = $this->authenticateUser();
});

// --- List Page ---

it('can render the tag list page', function () {
    livewire(ListTags::class)
        ->assertSuccessful();
});

it('can list tags', function () {
    $tags = Tag::factory()->count(3)->create();

    livewire(ListTags::class)
        ->assertCanSeeTableRecords($tags);
});

it('has the expected table columns', function () {
    livewire(ListTags::class)
        ->assertTableColumnExists('name')
        ->assertTableColumnExists('slug')
        ->assertTableColumnExists('color')
        ->assertTableColumnExists('created_at');
});

it('can search tags by name', function () {
    $tag = Tag::create(['name' => 'Bug', 'slug' => 'bug', 'color' => '#FF0000']);
    $other = Tag::create(['name' => 'Feature', 'slug' => 'feature', 'color' => '#00FF00']);

    livewire(ListTags::class)
        ->searchTable('Bug')
        ->assertCanSeeTableRecords([$tag])
        ->assertCanNotSeeTableRecords([$other]);
});

it('can sort tags by name', function () {
    $tagA = Tag::create(['name' => 'Alpha', 'slug' => 'alpha', 'color' => '#111111']);
    $tagZ = Tag::create(['name' => 'Zeta', 'slug' => 'zeta', 'color' => '#999999']);

    livewire(ListTags::class)
        ->sortTable('name')
        ->assertCanSeeTableRecords([$tagA, $tagZ], inOrder: true);
});

// --- Create Page ---

it('can render the create page', function () {
    livewire(CreateTag::class)
        ->assertSuccessful();
});

it('has the correct form fields', function () {
    livewire(CreateTag::class)
        ->assertFormFieldExists('name')
        ->assertFormFieldExists('slug')
        ->assertFormFieldExists('color');
});

it('can create a tag', function () {
    livewire(CreateTag::class)
        ->fillForm([
            'name' => 'Urgent',
            'slug' => 'urgent',
            'color' => '#FF0000',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('escalated_tags', [
        'name' => 'Urgent',
        'slug' => 'urgent',
        'color' => '#FF0000',
    ]);
});

it('requires a name when creating tag', function () {
    livewire(CreateTag::class)
        ->fillForm([
            'name' => '',
            'slug' => 'test-slug',
            'color' => '#FF0000',
        ])
        ->call('create')
        ->assertHasFormErrors(['name' => 'required']);
});

it('requires a slug when creating tag', function () {
    livewire(CreateTag::class)
        ->fillForm([
            'name' => 'Test',
            'slug' => '',
            'color' => '#FF0000',
        ])
        ->call('create')
        ->assertHasFormErrors(['slug' => 'required']);
});

it('requires unique slug when creating tag', function () {
    Tag::create(['name' => 'Existing', 'slug' => 'existing', 'color' => '#000000']);

    livewire(CreateTag::class)
        ->fillForm([
            'name' => 'Another',
            'slug' => 'existing',
            'color' => '#111111',
        ])
        ->call('create')
        ->assertHasFormErrors(['slug' => 'unique']);
});

it('requires color when creating tag', function () {
    livewire(CreateTag::class)
        ->fillForm([
            'name' => 'Test',
            'slug' => 'test',
            'color' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['color' => 'required']);
});

// --- Edit Page ---

it('can render the edit page', function () {
    $tag = Tag::create(['name' => 'Editable', 'slug' => 'editable', 'color' => '#123456']);

    livewire(EditTag::class, ['record' => $tag->id])
        ->assertSuccessful();
});

it('can fill the edit form with existing data', function () {
    $tag = Tag::create(['name' => 'Test Tag', 'slug' => 'test-tag', 'color' => '#ABCDEF']);

    livewire(EditTag::class, ['record' => $tag->id])
        ->assertFormSet([
            'name' => 'Test Tag',
            'slug' => 'test-tag',
            'color' => '#ABCDEF',
        ]);
});

it('can update a tag', function () {
    $tag = Tag::create(['name' => 'Old', 'slug' => 'old', 'color' => '#000000']);

    livewire(EditTag::class, ['record' => $tag->id])
        ->fillForm([
            'name' => 'New Name',
            'slug' => 'new-name',
            'color' => '#FF0000',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $tag->refresh();
    expect($tag->name)->toBe('New Name')
        ->and($tag->slug)->toBe('new-name')
        ->and($tag->color)->toBe('#FF0000');
});

// --- Resource Configuration ---

it('uses Tag as the model', function () {
    expect(TagResource::getModel())->toBe(Tag::class);
});

it('has navigation group from plugin', function () {
    expect(TagResource::getNavigationGroup())->toBe('Support');
});

it('has index, create, and edit pages', function () {
    $pages = TagResource::getPages();

    expect($pages)->toHaveKeys(['index', 'create', 'edit']);
});

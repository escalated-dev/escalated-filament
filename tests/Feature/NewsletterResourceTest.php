<?php

use Escalated\Filament\Pages\NewsletterSettings;
use Escalated\Filament\Resources\NewsletterListResource\Pages\ListNewsletterLists;
use Escalated\Filament\Resources\NewsletterResource\Pages\ListNewsletters;
use Escalated\Filament\Resources\NewsletterResource\Pages\ViewNewsletter;
use Escalated\Filament\Resources\NewsletterTemplateResource\Pages\ListNewsletterTemplates;
use Escalated\Laravel\Models\Newsletter\Newsletter;
use Escalated\Laravel\Models\Newsletter\NewsletterList;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->user = $this->authenticateUser();
});

$draftNewsletter = function (string $status = 'draft'): Newsletter {
    $list = NewsletterList::create(['name' => 'List', 'kind' => 'static']);

    return Newsletter::create([
        'subject' => 'Hello',
        'from_email' => 'from@example.com',
        'target_list_id' => $list->id,
        'status' => $status,
    ]);
};

it('renders the newsletters list page', function () {
    livewire(ListNewsletters::class)->assertSuccessful();
});

it('lists newsletters', function () use ($draftNewsletter) {
    $newsletter = $draftNewsletter();

    livewire(ListNewsletters::class)->assertCanSeeTableRecords([$newsletter]);
});

it('renders the newsletter lists page', function () {
    livewire(ListNewsletterLists::class)->assertSuccessful();
});

it('renders the newsletter templates page', function () {
    livewire(ListNewsletterTemplates::class)->assertSuccessful();
});

it('renders the newsletter settings page', function () {
    livewire(NewsletterSettings::class)->assertSuccessful();
});

it('exposes send, schedule and test-send actions on a draft newsletter view', function () use ($draftNewsletter) {
    $newsletter = $draftNewsletter('draft');

    livewire(ViewNewsletter::class, ['record' => $newsletter->getKey()])
        ->assertActionExists('send')
        ->assertActionExists('schedule')
        ->assertActionExists('testSend');
});

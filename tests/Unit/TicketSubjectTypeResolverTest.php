<?php

use Escalated\Filament\Support\TicketSubjectTypeResolver;
use Escalated\Filament\Tests\Models\FakeProject;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    if (! TicketSubjectTypeResolver::isAvailable()) {
        $this->markTestSkipped('Requires escalated-laravel#122 (TicketSubjectLink / attachSubject).');
    }
});

it('reports configured when allowlisted types are set', function () {
    config(['escalated.ticket_subjects.types' => [FakeProject::class]]);

    expect(TicketSubjectTypeResolver::isConfigured())->toBeTrue()
        ->and(TicketSubjectTypeResolver::allowedTypes())->toContain(FakeProject::class);
});

it('is not configured when the allowlist is empty', function () {
    config(['escalated.ticket_subjects.types' => []]);

    expect(TicketSubjectTypeResolver::isConfigured())->toBeFalse();
});

it('resolves only allowlisted types', function () {
    config(['escalated.ticket_subjects.types' => [FakeProject::class]]);

    expect(TicketSubjectTypeResolver::resolveModelClass(FakeProject::class))->toBe(FakeProject::class);

    expect(fn () => TicketSubjectTypeResolver::resolveModelClass('App\\Models\\Evil'))
        ->toThrow(ValidationException::class);
});

it('flattens morph alias keys into allowed types', function () {
    config(['escalated.ticket_subjects.types' => [
        'project' => FakeProject::class,
    ]]);

    expect(TicketSubjectTypeResolver::allowedTypes())->toContain('project', FakeProject::class);
});

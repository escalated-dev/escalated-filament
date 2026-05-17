<?php

declare(strict_types=1);

namespace {
    use Escalated\Filament\Tests\TestCase;
    use Filament\Schemas\Schema;
    use Illuminate\Validation\ValidationException;
    use Livewire\Features\SupportTesting\Testable;
    use Livewire\Livewire;

    uses(TestCase::class)->in('Unit', 'Feature');

    /**
     * Shorthand for Livewire::test() — replaces pestphp/pest-plugin-livewire
     * to avoid PHP version conflicts across Filament 3/4/5.
     */
    function livewire(string $component, array $params = []): Testable
    {
        return Livewire::test($component, $params);
    }

    /**
     * Assert Filament resource form validation using the same path as create/save ({@see Schema::getState()}).
     * Livewire's test `call('create'|'save')` round-trip does not populate {@see Testable::assertHasFormErrors()} for nested `data.*` rules.
     *
     * @param  list<string>  $fields  Field names without the `data.` prefix (e.g. `title`, `slug`).
     */
    function assertFilamentFormValidates(Testable $lw, array $fields): void
    {
        try {
            $lw->instance()->form->getState();
            expect(false)->toBeTrue('Expected ValidationException when validating form state.');
        } catch (ValidationException $e) {
            $errors = $e->errors();
            foreach ($fields as $field) {
                $key = str_contains($field, '.') ? $field : 'data.'.$field;
                expect($errors)->toHaveKey($key);
            }
        }
    }
}

namespace Pest\Livewire {
    use Livewire\Features\SupportTesting\Testable;
    use Livewire\Livewire;

    function livewire(string $component, array $params = []): Testable
    {
        return Livewire::test($component, $params);
    }
}

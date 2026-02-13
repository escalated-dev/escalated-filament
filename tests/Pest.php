<?php

use Escalated\Filament\Tests\TestCase;
use Livewire\Livewire;

uses(TestCase::class)->in('Unit', 'Feature');

/**
 * Shorthand for Livewire::test() — replaces pestphp/pest-plugin-livewire
 * to avoid PHP version conflicts across Filament 3/4/5.
 */
function livewire(string $component, array $params = []): \Livewire\Features\SupportTesting\Testable
{
    return Livewire::test($component, $params);
}

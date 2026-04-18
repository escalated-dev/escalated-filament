<?php

use Escalated\Filament\Pages\Reports;
use Illuminate\Support\Facades\DB;

/**
 * Verifies the Filament Reports page emits Postgres- and SQLite-
 * compatible minute-diff SQL, not just MySQL's TIMESTAMPDIFF.
 *
 * Same class of bug as escalated-laravel #59.
 */
function invokeMinutesDiff(string $driver, string $from, string $to): string
{
    $connection = Mockery::mock();
    $connection->shouldReceive('getDriverName')->andReturn($driver);
    DB::shouldReceive('connection')->andReturn($connection);

    return Reports::minutesDiffExpression($from, $to);
}

it('emits julianday * 1440 on sqlite', function () {
    expect(invokeMinutesDiff('sqlite', 'created_at', 'resolved_at'))
        ->toBe('(julianday(resolved_at) - julianday(created_at)) * 1440');
});

it('emits EXTRACT EPOCH / 60 on postgres (Filament-side regression guard)', function () {
    expect(invokeMinutesDiff('pgsql', 'created_at', 'resolved_at'))
        ->toBe('EXTRACT(EPOCH FROM (resolved_at - created_at)) / 60');
});

it('emits TIMESTAMPDIFF MINUTE on mysql', function () {
    expect(invokeMinutesDiff('mysql', 'created_at', 'resolved_at'))
        ->toBe('TIMESTAMPDIFF(MINUTE, created_at, resolved_at)');
});

it('never emits MySQL-specific TIMESTAMPDIFF on postgres', function () {
    expect(invokeMinutesDiff('pgsql', 'a', 'b'))->not->toContain('TIMESTAMPDIFF');
});

<?php

use Escalated\Filament\Resources\ApiTokenResource;
use Escalated\Filament\Resources\ApiTokenResource\Pages\CreateApiToken;
use Escalated\Filament\Resources\SkillResource;
use Escalated\Filament\Support\StaffSeat;
use Escalated\Filament\Tests\User;
use Escalated\Laravel\Contracts\TenantResolver;
use Escalated\Laravel\Models\ApiToken;
use Escalated\Laravel\Support\StaffAccess;
use Escalated\Laravel\Tenancy\TenantContext;
use Escalated\Laravel\Tenancy\UnconfiguredTenantResolver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

use function Pest\Livewire\livewire;

/*
 * Staff pickers in the panel go through escalated-laravel's StaffAccess, so
 * in tenant mode a host-global agent flag is not enough: the user also needs
 * an agent seat in the current account.
 */

function staffSeatUser(string $name): User
{
    return User::create([
        'name' => ucfirst($name),
        'email' => "{$name}@test.com",
        'password' => bcrypt('password'),
    ]);
}

it('issues API tokens only for users who pass the agent check', function () {
    $this->authenticateUser();
    $agent = staffSeatUser('agent');
    $customer = staffSeatUser('customer');
    Gate::define('escalated-agent', fn ($user) => $user->email !== 'customer@test.com');

    expect(ApiTokenResource::tokenUserOptions())->toHaveKey($agent->id)->not->toHaveKey($customer->id);

    livewire(CreateApiToken::class)
        ->fillForm(['name' => 'Customer token', 'tokenable_id' => $customer->id, 'abilities' => ['agent']])
        ->call('create');
    expect(ApiToken::query()->where('tokenable_id', $customer->id)->exists())->toBeFalse();

    livewire(CreateApiToken::class)
        ->fillForm(['name' => 'Agent token', 'tokenable_id' => $agent->id, 'abilities' => ['agent']])
        ->call('create')
        ->assertHasNoFormErrors();
    expect(ApiToken::query()->where('tokenable_id', $agent->id)->count())->toBe(1);
});

describe('tenant mode', function () {
    beforeEach(function () {
        if (! class_exists(StaffAccess::class)) {
            $this->markTestSkipped('Installed escalated-laravel predates tenant staff seats.');
        }

        $this->agent = staffSeatUser('agent');
        $this->customer = staffSeatUser('customer');

        // Both hold the host-global agent gate (TestCase grants it to everyone)
        // and are members of account "a", but only one holds an agent seat.
        $agentId = $this->agent->id;
        app()->instance(TenantResolver::class, new class($agentId, $this->customer->id) extends UnconfiguredTenantResolver
        {
            public function __construct(private int $agentId, private int $customerId) {}

            public function canAccess(Model $user, string $tenantId): bool
            {
                return $tenantId === 'a' && in_array($user->getKey(), [$this->agentId, $this->customerId], true);
            }

            public function isAgent(Model $user, string $tenantId): bool
            {
                return $tenantId === 'a' && $user->getKey() === $this->agentId;
            }

            public function isAdmin(Model $user, string $tenantId): bool
            {
                return $this->isAgent($user, $tenantId);
            }
        });
        config(['escalated.tenancy.enabled' => true]);
    });

    it('requires a tenant-local agent seat, not only the host gate', function () {
        app(TenantContext::class)->run('a', function () {
            expect(StaffSeat::isAgent($this->agent))->toBeTrue()
                ->and(StaffSeat::isAdmin($this->agent))->toBeTrue()
                ->and(StaffSeat::isAgent($this->customer))->toBeFalse()
                ->and(StaffSeat::isAdmin($this->customer))->toBeFalse();
        });

        app(TenantContext::class)->run('b', function () {
            expect(StaffSeat::isAgent($this->agent))->toBeFalse();
        });
    });

    it('limits API token users to agents seated in the current account', function () {
        $options = app(TenantContext::class)->run('a', fn () => ApiTokenResource::tokenUserOptions());

        expect(array_keys($options))->toBe([$this->agent->id]);
    });

    it('limits skill agents to agents seated in the current account', function () {
        $options = app(TenantContext::class)->run('a', fn () => SkillResource::agentUserOptions());

        expect(array_keys($options))->toBe([$this->agent->id]);
    });
});

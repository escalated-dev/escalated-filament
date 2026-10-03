<?php

use Escalated\Filament\EscalatedFilamentPlugin;
use Escalated\Filament\Pages\Dashboard;
use Escalated\Filament\Pages\EmailSettings;
use Escalated\Filament\Pages\ManagePlugins;
use Escalated\Filament\Pages\NewsletterSettings;
use Escalated\Filament\Pages\PublicTicketsSettings;
use Escalated\Filament\Pages\Reports;
use Escalated\Filament\Pages\Settings;
use Escalated\Filament\Pages\SsoSettings;
use Escalated\Filament\Resources\ApiTokenResource;
use Escalated\Filament\Resources\ArticleCategoryResource;
use Escalated\Filament\Resources\ArticleResource;
use Escalated\Filament\Resources\AuditLogResource;
use Escalated\Filament\Resources\AutomationResource;
use Escalated\Filament\Resources\BusinessScheduleResource;
use Escalated\Filament\Resources\CannedResponseResource;
use Escalated\Filament\Resources\CustomFieldResource;
use Escalated\Filament\Resources\DepartmentResource;
use Escalated\Filament\Resources\DepartmentResource\Pages\EditDepartment;
use Escalated\Filament\Resources\EscalationRuleResource;
use Escalated\Filament\Resources\MacroResource;
use Escalated\Filament\Resources\NewsletterListResource;
use Escalated\Filament\Resources\NewsletterResource;
use Escalated\Filament\Resources\NewsletterTemplateResource;
use Escalated\Filament\Resources\RoleResource;
use Escalated\Filament\Resources\SkillResource;
use Escalated\Filament\Resources\SlaPolicyResource;
use Escalated\Filament\Resources\TagResource;
use Escalated\Filament\Resources\TicketResource;
use Escalated\Filament\Resources\TicketResource\Pages\ViewTicket;
use Escalated\Filament\Resources\TicketStatusResource;
use Escalated\Filament\Resources\WebhookResource;
use Escalated\Filament\Tests\User;
use Escalated\Filament\Widgets\CsatOverviewWidget;
use Escalated\Filament\Widgets\RecentTicketsWidget;
use Escalated\Filament\Widgets\SlaBreachWidget;
use Escalated\Filament\Widgets\TicketsByPriorityChart;
use Escalated\Filament\Widgets\TicketsByStatusChart;
use Escalated\Filament\Widgets\TicketStatsOverview;
use Escalated\Laravel\Contracts\TenantResolver;
use Escalated\Laravel\Models\CannedResponse;
use Escalated\Laravel\Models\Department;
use Escalated\Laravel\Models\Ticket;
use Escalated\Laravel\Support\StaffAccess;
use Escalated\Laravel\Tenancy\TenantContext;
use Escalated\Laravel\Tenancy\UnconfiguredTenantResolver;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

use function Pest\Livewire\livewire;

/*
 * The host's canAccessPanel() decides who may enter the panel; the plugin's
 * agent and admin gates decide what they may do once inside. Every resource,
 * page and widget is gated, not just the ones escalated-laravel ships a
 * policy for.
 */

const PANEL_ADMIN_RESOURCES = [
    DepartmentResource::class,
    TagResource::class,
    SlaPolicyResource::class,
    EscalationRuleResource::class,
    MacroResource::class,
    ApiTokenResource::class,
    AutomationResource::class,
    WebhookResource::class,
    RoleResource::class,
    TicketStatusResource::class,
    SkillResource::class,
    CustomFieldResource::class,
    BusinessScheduleResource::class,
    ArticleResource::class,
    ArticleCategoryResource::class,
    AuditLogResource::class,
    NewsletterResource::class,
    NewsletterListResource::class,
    NewsletterTemplateResource::class,
];

const PANEL_AGENT_RESOURCES = [
    TicketResource::class,
    CannedResponseResource::class,
];

const PANEL_ADMIN_PAGES = [
    Reports::class,
    Settings::class,
    PublicTicketsSettings::class,
    SsoSettings::class,
    EmailSettings::class,
    NewsletterSettings::class,
    ManagePlugins::class,
];

const PANEL_AGENT_PAGES = [
    Dashboard::class,
];

const PANEL_WIDGETS = [
    TicketStatsOverview::class,
    TicketsByStatusChart::class,
    TicketsByPriorityChart::class,
    CsatOverviewWidget::class,
    RecentTicketsWidget::class,
    SlaBreachWidget::class,
];

/**
 * Key each class by its short name: a bare two-element list of class strings
 * looks like a callable to Pest and is not expanded into rows.
 *
 * @param  list<class-string>  $classes
 * @return array<string, array{class-string}>
 */
function panelDataset(array ...$lists): array
{
    return collect(array_merge(...$lists))
        ->mapWithKeys(fn (string $class) => [class_basename($class) => [$class]])
        ->all();
}

function panelUser(string $role): User
{
    return User::create([
        'name' => ucfirst($role),
        'email' => "{$role}@test.com",
        'password' => bcrypt('password'),
    ]);
}

/** @return array{index: ?class-string, create: ?class-string} */
function panelListAndCreatePages(string $resource): array
{
    $pages = collect($resource::getPages())->map(fn ($registration) => $registration->getPage());

    return [
        'index' => $pages->first(fn ($page) => is_subclass_of($page, ListRecords::class)),
        'create' => $pages->first(fn ($page) => is_subclass_of($page, CreateRecord::class)),
    ];
}

function expectResourceAccess(string $resource, bool $allowed): void
{
    $record = new ($resource::getModel());

    expect($resource::canViewAny())->toBe($allowed, "{$resource}::canViewAny");
    if (! $allowed) {
        expect($resource::canCreate())->toBeFalse("{$resource}::canCreate")
            ->and($resource::canEdit($record))->toBeFalse("{$resource}::canEdit")
            ->and($resource::canView($record))->toBeFalse("{$resource}::canView")
            ->and($resource::canDelete($record))->toBeFalse("{$resource}::canDelete")
            ->and($resource::canDeleteAny())->toBeFalse("{$resource}::canDeleteAny");
    }

    $pages = panelListAndCreatePages($resource);

    $index = livewire($pages['index']);
    $allowed ? $index->assertSuccessful() : $index->assertForbidden();

    if ($pages['create'] !== null && ! $allowed) {
        livewire($pages['create'])->assertForbidden();
    }
}

beforeEach(function () {
    config()->set('escalated.newsletters.enabled', true);
    config()->set('escalated.plugins.enabled', true);

    Gate::define('escalated-agent', fn ($user) => in_array($user->email, ['agent@test.com', 'admin@test.com'], true));
    Gate::define('escalated-admin', fn ($user) => $user->email === 'admin@test.com');
});

describe('a panel user without the agent or admin gate', function () {
    beforeEach(function () {
        $this->actingAs(panelUser('customer'));
    });

    it('is refused every admin resource', function (string $resource) {
        expectResourceAccess($resource, false);
    })->with(panelDataset(PANEL_ADMIN_RESOURCES));

    it('is refused every agent resource', function (string $resource) {
        expectResourceAccess($resource, false);
    })->with(panelDataset(PANEL_AGENT_RESOURCES));

    it('is refused every page', function (string $page) {
        expect($page::canAccess())->toBeFalse();
        livewire($page)->assertForbidden();
    })->with(panelDataset(PANEL_ADMIN_PAGES, PANEL_AGENT_PAGES));

    it('sees no dashboard widgets', function (string $widget) {
        expect($widget::canView())->toBeFalse();
    })->with(panelDataset(PANEL_WIDGETS));

    it('cannot open a ticket or edit a department by URL', function () {
        $ticket = Ticket::factory()->create();
        $department = Department::factory()->create();

        livewire(ViewTicket::class, ['record' => $ticket->getRouteKey()])->assertForbidden();
        livewire(EditDepartment::class, ['record' => $department->getRouteKey()])->assertForbidden();
    });

    it('gets 403 from the panel routes over HTTP', function () {
        $this->get(WebhookResource::getUrl('index'))->assertForbidden();
        $this->get(ApiTokenResource::getUrl('create'))->assertForbidden();
        $this->get(Settings::getUrl())->assertForbidden();
    });
});

describe('an agent', function () {
    beforeEach(function () {
        $this->actingAs(panelUser('agent'));
    });

    it('reaches the agent resources', function (string $resource) {
        expectResourceAccess($resource, true);
    })->with(panelDataset(PANEL_AGENT_RESOURCES));

    it('reaches the support dashboard and its widgets', function () {
        expect(Dashboard::canAccess())->toBeTrue();
        livewire(Dashboard::class)->assertSuccessful();

        foreach (PANEL_WIDGETS as $widget) {
            expect($widget::canView())->toBeTrue($widget);
        }
    });

    it('can open a ticket', function () {
        $ticket = Ticket::factory()->create();

        livewire(ViewTicket::class, ['record' => $ticket->getRouteKey()])->assertSuccessful();
    });

    it('is refused every admin resource', function (string $resource) {
        expectResourceAccess($resource, false);
    })->with(panelDataset(PANEL_ADMIN_RESOURCES));

    it('is refused every admin page', function (string $page) {
        expect($page::canAccess())->toBeFalse();
        livewire($page)->assertForbidden();
    })->with(panelDataset(PANEL_ADMIN_PAGES));
});

describe('an admin', function () {
    beforeEach(function () {
        $this->actingAs(panelUser('admin'));
    });

    it('reaches every resource', function (string $resource) {
        expectResourceAccess($resource, true);
    })->with(panelDataset(PANEL_ADMIN_RESOURCES, PANEL_AGENT_RESOURCES));

    it('reaches every page', function (string $page) {
        expect($page::canAccess())->toBeTrue();
    })->with(panelDataset(PANEL_ADMIN_PAGES, PANEL_AGENT_PAGES));

    it('can edit a department', function () {
        $department = Department::factory()->create();

        livewire(EditDepartment::class, ['record' => $department->getRouteKey()])->assertSuccessful();
    });

    it('reaches tickets with only the admin gate', function () {
        Gate::define('escalated-agent', fn () => false);

        expect(TicketResource::canViewAny())->toBeTrue()
            ->and(Dashboard::canAccess())->toBeTrue();
    });
});

describe('custom gate names', function () {
    beforeEach(function () {
        app(EscalatedFilamentPlugin::class)->agentGate('support-agent')->adminGate('support-admin');
    });

    afterEach(function () {
        app(EscalatedFilamentPlugin::class)->agentGate('escalated-agent')->adminGate('escalated-admin');
    });

    it('checks the gates configured on the plugin, not the defaults', function () {
        Gate::define('support-agent', fn ($user) => $user->email === 'helper@test.com');
        Gate::define('support-admin', fn ($user) => $user->email === 'boss@test.com');

        // Passes the default gates but not the configured ones.
        $this->actingAs(panelUser('admin'));
        expect(TicketResource::canViewAny())->toBeFalse()
            ->and(WebhookResource::canViewAny())->toBeFalse()
            ->and(Settings::canAccess())->toBeFalse();

        $this->actingAs(panelUser('helper'));
        expect(TicketResource::canViewAny())->toBeTrue()
            ->and(WebhookResource::canViewAny())->toBeFalse();

        $this->actingAs(panelUser('boss'));
        expect(TicketResource::canViewAny())->toBeTrue()
            ->and(WebhookResource::canViewAny())->toBeTrue()
            ->and(Settings::canAccess())->toBeTrue();
    });
});

it('keeps the escalated-laravel policies in force for staff', function () {
    // CannedResponsePolicy still limits private responses to their author.
    $this->actingAs(panelUser('agent'));
    $other = panelUser('other');

    $private = new CannedResponse(['is_shared' => false]);
    $private->created_by = $other->getKey();

    expect(CannedResponseResource::canEdit($private))->toBeFalse();
});

describe('tenant mode', function () {
    beforeEach(function () {
        if (! class_exists(StaffAccess::class)) {
            $this->markTestSkipped('Installed escalated-laravel predates tenant staff seats.');
        }

        // Every user passes the host-global gates; only the seated agent
        // holds an agent seat in account "a", and nobody holds an admin seat.
        Gate::define('escalated-agent', fn () => true);
        Gate::define('escalated-admin', fn () => true);
        Gate::define('support-agent', fn () => true);

        $this->agent = panelUser('agent');
        $this->customer = panelUser('customer');

        app()->instance(TenantResolver::class, new class($this->agent->id, $this->customer->id) extends UnconfiguredTenantResolver
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
                return false;
            }
        });
        config(['escalated.tenancy.enabled' => true]);
    });

    afterEach(function () {
        app(EscalatedFilamentPlugin::class)->agentGate('escalated-agent');
    });

    it('requires a tenant-local seat on top of the host gate', function () {
        $access = fn (User $user) => app(TenantContext::class)->run('a', function () use ($user) {
            $this->actingAs($user);

            return [TicketResource::canViewAny(), WebhookResource::canViewAny(), Settings::canAccess()];
        });

        expect($access($this->agent))->toBe([true, false, false])
            ->and($access($this->customer))->toBe([false, false, false]);
    });

    it('still requires the seat when the plugin names its own gate', function () {
        app(EscalatedFilamentPlugin::class)->agentGate('support-agent');

        $canViewTickets = fn (User $user) => app(TenantContext::class)->run('a', function () use ($user) {
            $this->actingAs($user);

            return TicketResource::canViewAny();
        });

        expect($canViewTickets($this->agent))->toBeTrue()
            ->and($canViewTickets($this->customer))->toBeFalse();
    });
});

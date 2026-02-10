# Escalated for Filament

A [Filament v3](https://filamentphp.com) admin panel plugin for the [Escalated](https://github.com/escalated-dev/escalated-laravel) support ticket system. Manage tickets, departments, SLA policies, escalation rules, macros, and more — all from within your existing Filament admin panel.

## How It Works

Escalated for Filament is a **Filament plugin wrapper** around [`escalated-laravel`](https://github.com/escalated-dev/escalated-laravel). It does not duplicate any business logic. Instead, it provides Filament Resources, Pages, Widgets, and Actions that call the same services, models, and events from the core Laravel package. This means:

- All ticket lifecycle logic, SLA calculations, and escalation rules come from `escalated-laravel`
- Database tables, migrations, and configuration are managed by the core package
- Events, notifications, and webhooks fire exactly as they would from the Inertia UI
- You get a native Filament experience without maintaining a separate codebase

## Requirements

- PHP 8.2+
- Laravel 11 or 12
- Filament 3.x
- escalated-dev/escalated-laravel ^0.4

## Installation

### 1. Install the packages

```bash
composer require escalated-dev/escalated-laravel escalated-dev/escalated-filament
```

If you already have `escalated-laravel` installed, just add the Filament plugin:

```bash
composer require escalated-dev/escalated-filament
```

### 2. Run the Escalated installer (if not already done)

```bash
php artisan escalated:install
php artisan migrate
```

### 3. Define authorization gates

In a service provider (e.g., `AppServiceProvider`):

```php
use Illuminate\Support\Facades\Gate;

Gate::define('escalated-admin', fn ($user) => $user->is_admin);
Gate::define('escalated-agent', fn ($user) => $user->is_agent || $user->is_admin);
```

### 4. Register the plugin in your Filament panel

```php
use Escalated\Filament\EscalatedFilamentPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugin(
            EscalatedFilamentPlugin::make()
                ->navigationGroup('Support')
                ->agentGate('escalated-agent')
                ->adminGate('escalated-admin')
        );
}
```

You're live. Visit your Filament panel — a **Support** navigation group will appear with all ticket management resources.

## Features

### Resources

- **TicketResource** — Full ticket management with list, view, and create pages
  - Filterable by status, priority, department, agent, tags, SLA
  - Quick filter tabs: All, My Tickets, Unassigned, Urgent, SLA Breaching
  - Bulk actions: Assign, Change Status, Change Priority, Add Tags, Close, Delete
  - View page with conversation thread, sidebar details, SLA info, satisfaction rating
  - Header actions: Reply, Note, Assign, Status, Priority, Follow, Macro, Resolve, Close, Reopen
- **DepartmentResource** — CRUD for support departments with agent assignment
- **TagResource** — CRUD for ticket tags with color picker
- **SlaPolicyResource** — SLA policy management with per-priority response/resolution times
- **EscalationRuleResource** — Condition/action builder for automatic escalation rules
- **CannedResponseResource** — Pre-written response templates with categories
- **MacroResource** — Multi-action automation macros with reorderable steps

### Dashboard Widgets

- **TicketStatsOverview** — Key metrics: My Open, Unassigned, Total Open, SLA Breached, Resolved Today, CSAT
- **TicketsByStatusChart** — Doughnut chart of ticket distribution by status
- **TicketsByPriorityChart** — Bar chart of open tickets by priority
- **CsatOverviewWidget** — Customer satisfaction metrics: Average Rating, Total Ratings, Satisfaction Rate
- **RecentTicketsWidget** — Table of the 5 most recent tickets
- **SlaBreachWidget** — Table of tickets with breached SLA targets

### Pages

- **Dashboard** — Support dashboard with all widgets
- **Reports** — Date-range analytics with stats, department breakdown, and timeline
- **Settings** — Admin settings for reference prefix, guest tickets, auto-close, attachment limits

### Relation Managers

- **RepliesRelationManager** — Reply thread with internal notes, pinning, and canned response insertion
- **ActivitiesRelationManager** — Read-only audit log of all ticket activities
- **FollowersRelationManager** — Manage ticket followers

### Reusable Actions

- `AssignTicketAction` — Assign a ticket to an agent
- `ChangeStatusAction` — Change ticket status
- `ChangePriorityAction` — Change ticket priority
- `ApplyMacroAction` — Apply a macro to a ticket
- `FollowTicketAction` — Toggle following a ticket
- `PinReplyAction` — Pin/unpin internal notes

### Custom Livewire Components

- **TicketConversation** — Full conversation thread with reply composer, canned response insertion, and note pinning
- **SatisfactionRating** — Display customer satisfaction rating with star visualization

## Configuration

The plugin is configured through method chaining on the plugin instance:

```php
EscalatedFilamentPlugin::make()
    ->navigationGroup('Support')    // Navigation group label (default: 'Support')
    ->agentGate('escalated-agent')  // Gate for agent access (default: 'escalated-agent')
    ->adminGate('escalated-admin')  // Gate for admin access (default: 'escalated-admin')
```

All other configuration (SLA, hosting modes, notifications, etc.) is managed by the core `escalated-laravel` package in `config/escalated.php`. See the [escalated-laravel README](https://github.com/escalated-dev/escalated-laravel) for full configuration reference.

## Publishing Views

```bash
php artisan vendor:publish --tag=escalated-filament-views
```

## Screenshots

_Coming soon._

## Also Available For

- **[Escalated for Laravel](https://github.com/escalated-dev/escalated-laravel)** — Laravel Composer package
- **[Escalated for Rails](https://github.com/escalated-dev/escalated-rails)** — Ruby on Rails engine
- **[Escalated for Django](https://github.com/escalated-dev/escalated-django)** — Django reusable app
- **[Escalated for AdonisJS](https://github.com/escalated-dev/escalated-adonis)** — AdonisJS v6 package
- **[Escalated for Filament](https://github.com/escalated-dev/escalated-filament)** — Filament v3 admin panel plugin (you are here)
- **[Shared Frontend](https://github.com/escalated-dev/escalated)** — Vue 3 + Inertia.js UI components

Same architecture, same ticket system — native Filament experience for Laravel admin panels.

## License

MIT

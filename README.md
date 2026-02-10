# Escalated Filament

A [Filament v3](https://filamentphp.com) admin panel plugin for the [Escalated](https://github.com/escalated-dev/escalated-laravel) support ticket system.

## Requirements

- PHP 8.2+
- Laravel 11 or 12
- Filament 3.x
- escalated-dev/escalated-laravel ^0.4

## Installation

```bash
composer require escalated-dev/escalated-filament
```

## Setup

Register the plugin in your Filament panel provider:

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

## Features

### Resources

- **TicketResource** - Full ticket management with list, view, and create pages
  - Filterable by status, priority, department, agent, tags, SLA
  - Quick filter tabs: All, My Tickets, Unassigned, Urgent, SLA Breaching
  - Bulk actions: Assign, Change Status, Change Priority, Add Tags, Close, Delete
  - View page with conversation thread, sidebar details, SLA info, satisfaction rating
  - Header actions: Reply, Note, Assign, Status, Priority, Follow, Macro, Resolve, Close, Reopen
- **DepartmentResource** - CRUD for support departments with agent assignment
- **TagResource** - CRUD for ticket tags with color picker
- **SlaPolicyResource** - SLA policy management with per-priority response/resolution times
- **EscalationRuleResource** - Condition/action builder for automatic escalation rules
- **CannedResponseResource** - Pre-written response templates with categories
- **MacroResource** - Multi-action automation macros with reorderable steps

### Dashboard Widgets

- **TicketStatsOverview** - Key metrics: My Open, Unassigned, Total Open, SLA Breached, Resolved Today, CSAT
- **TicketsByStatusChart** - Doughnut chart of ticket distribution by status
- **TicketsByPriorityChart** - Bar chart of open tickets by priority
- **CsatOverviewWidget** - Customer satisfaction metrics: Average Rating, Total Ratings, Satisfaction Rate
- **RecentTicketsWidget** - Table of the 5 most recent tickets
- **SlaBreachWidget** - Table of tickets with breached SLA targets

### Pages

- **Dashboard** - Support dashboard with all widgets
- **Reports** - Date-range analytics with stats, department breakdown, and timeline
- **Settings** - Admin settings for reference prefix, guest tickets, auto-close, attachment limits

### Relation Managers

- **RepliesRelationManager** - Reply thread with internal notes, pinning, and canned response insertion
- **ActivitiesRelationManager** - Read-only audit log of all ticket activities
- **FollowersRelationManager** - Manage ticket followers

### Reusable Actions

- `AssignTicketAction` - Assign a ticket to an agent
- `ChangeStatusAction` - Change ticket status
- `ChangePriorityAction` - Change ticket priority
- `ApplyMacroAction` - Apply a macro to a ticket
- `FollowTicketAction` - Toggle following a ticket
- `PinReplyAction` - Pin/unpin internal notes

### Custom Livewire Components

- **TicketConversation** - Full conversation thread with reply composer, canned response insertion, and note pinning
- **SatisfactionRating** - Display customer satisfaction rating with star visualization

## Configuration

The plugin is configured through method chaining on the plugin instance:

```php
EscalatedFilamentPlugin::make()
    ->navigationGroup('Support')    // Navigation group label (default: 'Support')
    ->agentGate('escalated-agent')  // Gate for agent access (default: 'escalated-agent')
    ->adminGate('escalated-admin')  // Gate for admin access (default: 'escalated-admin')
```

## Publishing Views

```bash
php artisan vendor:publish --tag=escalated-filament-views
```

## License

MIT

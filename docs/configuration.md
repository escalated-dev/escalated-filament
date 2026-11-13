# Configuration

## Configuration

The plugin is configured through method chaining on the plugin instance:

```php
EscalatedFilamentPlugin::make()
    ->navigationGroup('Support')    // Navigation group label (default: 'Support')
    ->agentGate('escalated-agent')  // Gate for agent access (default: 'escalated-agent')
    ->adminGate('escalated-admin')  // Gate for admin access (default: 'escalated-admin')
```

### Access control

Your panel's `canAccessPanel()` decides who can sign in to the panel. The two gates decide what they can use once inside, mirroring the `escalated-laravel` routes:

| Surface | Required |
|---|---|
| Tickets, canned responses, the support dashboard and its widgets | agent gate **or** admin gate |
| Everything else: departments, tags, SLA policies, escalation rules, macros, automations, webhooks, API tokens, roles, statuses, skills, custom fields, business hours, KB articles and categories, audit log, newsletters, reports, and every settings page | admin gate |

A panel user who passes neither gate gets a 403 on every Escalated page, and the navigation items are hidden. Model policies shipped by `escalated-laravel` (for example, private canned responses stay editable only by their author) still apply on top. In tenant mode the user also needs a staff seat in the current account, whichever gate names you configure.

All other configuration (SLA, hosting modes, notifications, etc.) is managed by the core `escalated-laravel` package in `config/escalated.php`. See the [escalated-laravel README](https://github.com/escalated-dev/escalated-laravel) for full configuration reference.

## Publishing Views

```bash
php artisan vendor:publish --tag=escalated-filament-views
```

## Internationalization

UI strings shown in this plugin's Filament resources, pages, and widgets resolve through the standard Escalated translation chain. The chain is configured in the underlying [`escalated-laravel`](https://github.com/escalated-dev/escalated-laravel) package and reads, in order:

1. App-level overrides (`lang/vendor/escalated/{locale}/{group}.php`)
2. The central [`escalated-dev/locale`](https://github.com/escalated-dev/escalated-locale) Composer package — single source of truth across all framework plugins
3. This plugin's bundled fallback strings under `resources/lang/` (namespaced as `escalated-filament::*`)

The central package is pulled in as an explicit dependency so that even if you only install `escalated-filament` without the rest of the stack the shared keys resolve correctly. To override a Filament-specific string in your application, publish:

```bash
php artisan vendor:publish --tag=escalated-filament-lang
```

then edit the files under `lang/vendor/escalated-filament/{locale}/`.

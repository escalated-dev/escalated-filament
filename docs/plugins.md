# Building Plugins for Escalated Filament

Escalated Filament uses the **same plugin system as escalated-laravel** — it inherits the backend, hook API, and plugin architecture. The only difference is that the admin panel is powered by **Filament** instead of Inertia.js.

For the complete plugin API and examples, see the [escalated-laravel plugin guide](../../escalated-laravel/docs/plugins.md). This document highlights what's different when working with Filament.

## Plugin Structure

**Identical to escalated-laravel:**

```
my-plugin/
  plugin.json      # Manifest (required)
  Plugin.php       # Entry point (required)
```

### plugin.json

Same format as escalated-laravel. See the Laravel guide for the full specification.

```json
{
    "name": "My Plugin",
    "slug": "my-plugin",
    "description": "A short description of what this plugin does.",
    "version": "1.0.0",
    "author": "Your Name",
    "main_file": "Plugin.php"
}
```

## Hook API

**Identical to escalated-laravel.** All hooks use the same PHP helper functions:

### Action Hooks

```php
escalated_add_action(string $tag, callable $callback, int $priority = 10): void
escalated_do_action(string $tag, ...$args): void
escalated_has_action(string $tag): bool
escalated_remove_action(string $tag, ?callable $callback = null): void
```

### Filter Hooks

```php
escalated_add_filter(string $tag, callable $callback, int $priority = 10): void
escalated_apply_filters(string $tag, mixed $value, ...$args): mixed
escalated_has_filter(string $tag): bool
escalated_remove_filter(string $tag, ?callable $callback = null): void
```

## Lifecycle Hooks

**Identical to escalated-laravel:**

| Hook | Args | When |
|------|------|------|
| `escalated_plugin_loaded` | `$slug, $manifest` | Plugin file is loaded |
| `escalated_plugin_activated` | `$slug` | Plugin is activated |
| `escalated_plugin_activated_{slug}` | — | Your specific plugin is activated |
| `escalated_plugin_deactivated` | `$slug` | Plugin is deactivated |
| `escalated_plugin_deactivated_{slug}` | — | Your specific plugin is deactivated |
| `escalated_plugin_uninstalling` | `$slug` | Plugin is about to be deleted |
| `escalated_plugin_uninstalling_{slug}` | — | Your specific plugin is about to be deleted |

```php
escalated_add_action('escalated_plugin_activated_my-plugin', function () {
    // Run migrations, seed data, etc.
});

escalated_add_action('escalated_plugin_uninstalling_my-plugin', function () {
    // Clean up database tables, cached files, etc.
});
```

## UI Helpers

**Same API as escalated-laravel:**

```php
// Menu items
escalated_register_menu_item([
    'label' => 'Billing',
    'url' => '/support/admin/billing',
    'icon' => 'heroicon-o-currency-dollar',
    'section' => 'admin',
    'priority' => 50,
]);

// Custom pages
escalated_register_page(
    'admin/billing',
    'Escalated/Admin/Billing',
    ['middleware' => ['auth']]
);

// Dashboard widgets
escalated_register_dashboard_widget([
    'id' => 'billing-summary',
    'label' => 'Billing Summary',
    'component' => 'BillingSummaryWidget',
    'section' => 'agent',
    'priority' => 10,
]);

// Page components (slots)
escalated_add_page_component(
    'ticket-detail',
    'sidebar',
    [
        'component' => 'BillingInfo',
        'props' => ['show_total' => true],
        'priority' => 10,
    ]
);
```

## Distribution

### ZIP Upload (Local Plugins)

1. Create a ZIP file containing your plugin folder at the root:
   ```
   my-plugin.zip
     └── my-plugin/
           ├── plugin.json
           └── Plugin.php
   ```
2. Go to **Admin > Plugins** in the Filament panel and upload the ZIP file.
3. Toggle the **Active** switch to activate the plugin.

Uploaded plugins are stored in `app/Plugins/Escalated/` (same location as escalated-laravel).

### Composer Package

Any Composer package that includes a `plugin.json` at its root is automatically detected:

```bash
composer require acme/escalated-billing
```

Composer plugins appear in the Filament admin panel with a **composer** badge. They cannot be deleted from the UI — use `composer remove` instead.

**Composer plugin slugs** are derived from the package name: `acme/escalated-billing` becomes `acme--escalated-billing`.

## What's Different in Filament

### Plugin Management UI

The plugin management page uses **Filament's table builder** instead of Inertia.js Vue components. You'll find it at **Admin > Plugins** in the Filament panel.

Features:
- Upload ZIP files
- Activate/deactivate plugins with a toggle switch
- Delete local plugins (composer plugins cannot be deleted)
- View plugin metadata (name, version, author, description)
- Source badge showing whether a plugin is **local** or **composer**

### Filament-Specific Integration

Plugins that want to add Filament resources, pages, or widgets can use the **standard Filament plugin API** in addition to Escalated's hook system:

```php
<?php

namespace Acme\EscalatedBilling;

use Filament\Contracts\Plugin;
use Filament\Panel;

class BillingPlugin implements Plugin
{
    public function getId(): string
    {
        return 'billing';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            Resources\InvoiceResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // Boot logic
    }
}
```

Then register it in your `Plugin.php`:

```php
<?php

use Filament\Facades\Filament;
use Acme\EscalatedBilling\BillingPlugin;

Filament::getCurrentPanel()?->plugin(new BillingPlugin());
```

However, **the Escalated hook system works identically to escalated-laravel** since escalated-filament depends on it.

## Full Example: Slack Notifier Plugin

**Identical to escalated-laravel.**

```
slack-notifier/
  plugin.json
  Plugin.php
```

**plugin.json:**
```json
{
    "name": "Slack Notifier",
    "slug": "slack-notifier",
    "description": "Posts a message to Slack when a new ticket is created.",
    "version": "1.0.0",
    "author": "Acme Corp",
    "main_file": "Plugin.php"
}
```

**Plugin.php:**
```php
<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

escalated_add_action('escalated_plugin_activated_slack-notifier', function () {
    Log::info('Slack Notifier plugin activated');
});

escalated_add_action('escalated_ticket_created', function ($ticket) {
    $webhookUrl = config('services.slack.webhook_url');

    if (! $webhookUrl) {
        return;
    }

    Http::post($webhookUrl, [
        'text' => "New ticket *{$ticket->reference}*: {$ticket->subject}",
    ]);
});

escalated_add_action('escalated_plugin_uninstalling_slack-notifier', function () {
    Log::info('Slack Notifier plugin uninstalled');
});
```

## Full Example: Composer Package

**Identical to escalated-laravel.**

**composer.json:**
```json
{
    "name": "acme/escalated-billing",
    "description": "Billing integration for Escalated",
    "type": "library",
    "require": {
        "php": "^8.1"
    },
    "autoload": {
        "psr-4": {
            "Acme\\EscalatedBilling\\": "src/"
        }
    }
}
```

**plugin.json:**
```json
{
    "name": "Billing Integration",
    "slug": "acme--escalated-billing",
    "description": "Adds billing and invoicing to Escalated.",
    "version": "2.0.0",
    "author": "Acme Corp",
    "main_file": "Plugin.php"
}
```

**Plugin.php:**
```php
<?php

use Acme\EscalatedBilling\BillingService;

escalated_add_action('escalated_ticket_created', function ($ticket) {
    app(BillingService::class)->trackTicket($ticket);
});

escalated_register_menu_item([
    'label' => 'Billing',
    'url' => '/support/admin/billing',
    'icon' => 'heroicon-o-currency-dollar',
    'section' => 'admin',
]);
```

Since Composer handles autoloading, your `Plugin.php` can use classes from `src/` without any manual `require` statements.

## Tips

- **Keep Plugin.php lightweight.** Register hooks and delegate to service classes.
- **Use activation hooks** to run migrations or seed data on first activation.
- **Use uninstall hooks** to clean up database tables when your plugin is removed.
- **Namespace your hooks** to avoid collisions: `escalated_myplugin_custom_action`.
- **Test locally** by placing your plugin folder in `app/Plugins/Escalated/` and activating it from the Filament admin panel.
- **Composer plugins** benefit from PSR-4 autoloading, testing infrastructure, and version management via Packagist.
- **The plugin system is shared** between escalated-laravel and escalated-filament — plugins are backend-agnostic and work in both environments.

# Installation

## Requirements

- PHP 8.2+
- Laravel 11 or 12
- Filament 3.x, 4.x, or 5.x
- escalated-dev/escalated-laravel ^0.5

### Version Compatibility

| escalated-filament | Filament | Laravel | PHP  |
|--------------------|----------|---------|------|
| 0.5.x              | 3.x, 4.x, 5.x | 11, 12  | 8.2+ |

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

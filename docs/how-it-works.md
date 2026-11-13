# How it works

## How It Works

Escalated for Filament is a **Filament plugin wrapper** around [`escalated-laravel`](https://github.com/escalated-dev/escalated-laravel). It does not duplicate any business logic. Instead, it provides Filament Resources, Pages, Widgets, and Actions that call the same services, models, and events from the core Laravel package. This means:

- All ticket lifecycle logic, SLA calculations, and escalation rules come from `escalated-laravel`
- Database tables, migrations, and configuration are managed by the core package
- Events, notifications, and webhooks fire exactly as they would from the Inertia UI
- You get a native Filament experience without maintaining a separate codebase

> **Note:** This package uses Filament's native Livewire + Blade components (tables, forms, info lists, actions, widgets) rather than the custom Vue 3 + Inertia.js UI from the [`@escalated-dev/escalated`](https://github.com/escalated-dev/escalated) frontend package. The core functionality is the same — same models, services, database, and business logic — but the UI look-and-feel follows Filament's design system. Some interactions may differ slightly (e.g., Filament modals vs. inline forms, Filament table filters vs. custom filter components). If you need pixel-perfect parity with the Inertia frontend, use `escalated-laravel` directly with the shared Vue components instead.

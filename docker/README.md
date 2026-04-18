# Escalated Filament — Docker demo

A one-command demo of the `escalated-dev/escalated-filament` plugin running inside a throwaway Laravel 12 + Filament 4 host app.

## Requirements

- Docker + Docker Compose
- ~500 MB disk space

## Run it

```bash
cd docker
cp .env.example .env          # optional — only needed if you want to change ports
docker compose up --build
```

Then open:

- **http://localhost:8000/demo** — click a seeded staff user to log in.
- **http://localhost:8000/admin** — the Filament admin panel (you'll be redirected here after clicking a user on `/demo`).
- **http://localhost:8025** — Mailpit UI for any outbound email the demo generates.

## What's inside

- **app** — PHP 8.3 + the Filament plugin + a minimal Laravel 12 host app.
  The host app at `docker/host-app/` composer-installs `escalated-filament` from the repo root via a path repository (so edits to `../src/` show up on rebuild) and pulls `escalated-laravel` from Packagist (`^1.2`). A single Filament `AdminPanelProvider` at `/admin` registers `EscalatedFilamentPlugin`.
- **db** — Postgres 16 (alpine).
- **mailpit** — SMTP sink with a browser UI for eyeballing notification templates.

Every `docker compose up` starts fresh:

- `migrate:fresh` wipes the database
- `escalated:install` (from the base Laravel package) publishes config, migrations, and seeds default permissions
- `DemoSeeder` loads ~55 tickets, 10 users (5 staff, 5 customers), departments, SLAs, macros, KB articles — so the Filament panel is populated on first login

## Seeded users

Only staff appear on `/demo` (this demo is Filament-admin-facing — customers don't have a Filament UI here):

| Role     | Email                          | Password   |
|----------|--------------------------------|------------|
| Admin    | alice@demo.test                | `password` |
| Agents   | bob / carol / dan @demo.test   | `password` |
| Light    | ellie@demo.test                | `password` |

Click-to-login routes them straight into `/admin`.

## Resetting

```bash
docker compose down -v         # nothing is persisted
docker compose up --build      # if you changed plugin source or the host skeleton
```

## Scope

- Uses `php artisan serve` (single-process PHP server). Fine for clicking around.
- No nginx, no queue worker, no Redis, no separate auth scaffolding — Filament's built-in login form sits behind `/admin`.
- `QUEUE_CONNECTION=sync` — jobs run in-process.
- `APP_KEY` is a known static value so the image doesn't need a secret.
- `APP_ENV=demo` is required to expose the click-to-login routes. They hard-abort in any other environment.

## Known issues

- The **Reports** page on `/admin/support-reports` uses MySQL-specific `TIMESTAMPDIFF` in older versions. This plugin's fix ships after PR #20 is released; until then the Reports page 500s on Postgres.
- `escalated-laravel` v1.2.x still has [escalated-laravel#59](https://github.com/escalated-dev/escalated-laravel/issues/59) for its own Reports path — doesn't affect the Filament demo because Filament doesn't hit that controller, but will if you click around to routes under `/support/admin/reports` (the Inertia side of the base package).

# Changelog

All notable changes to `escalated-filament` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.4.0] - 2026-10-08

This release enforces the panel's agent and admin gates and requires
escalated-laravel 1.9. Read **Upgrading** before deploying.

### Upgrading
- **`agentGate()` / `adminGate()` are now enforced.** Tickets, canned responses
  and the support dashboard require the agent or admin gate; every other
  Escalated resource and page requires the admin gate. Panel users who pass
  neither get a 403 and see no Escalated navigation. Make sure your support
  staff pass the configured gates (by default `escalated-agent` /
  `escalated-admin`, or the names passed to the plugin) before upgrading, or
  they lose access to the panel's Escalated pages.
- **Requires `escalated-dev/escalated-laravel` ^1.9.0.** Follow that release's
  upgrading notes first (frontend 0.12, migrations, private attachments,
  verified guest access, requester-only inbound email replies, tenant staff
  seats).
- **Tenant mode needs a staff seat in the current account.** With
  escalated-laravel tenancy enabled, panel access, the API token owner picker
  and the skill agent picker go through `StaffAccess` (host gate plus the
  tenant-local seat from `TenantResolver::isAgent/isAdmin`). A user with the
  global flag but no seat in the current account is refused and is not offered
  in the pickers.

### Added
- `Support\StaffSeat` checks staff access through escalated-laravel's
  `StaffAccess`, and `Support\PanelAccess` resolves the plugin's gate names.
  The API token user picker (`ApiTokenResource::tokenUserOptions()`) and the
  skill agent picker list only seated agents. (#49, #50)
- README "Access control" section, also in the translated READMEs. (#50, #51)

### Fixed
- The Livewire `ViewErrorBag` vendor patch script matches Livewire 4.4 as well
  as 4.3, so the test suite runs again on current Filament 5.7. (#47)

### Changed
- **The `agentGate()` / `adminGate()` settings are now enforced.** They were documented but never read, so every resource and page without an `escalated-laravel` model policy (webhooks, API tokens, roles, automations, macros, skills, custom fields, KB articles, newsletters, reports, settings, and more) was open to anyone who could sign in to the panel, and the ticket list was too. Tickets, canned responses and the support dashboard now require the agent or admin gate; every other resource and page requires the admin gate. Users without either gate get a 403 and see no Escalated navigation. Existing model policies still apply on top, and in tenant mode a staff seat in the current account is still required. **Upgrade note:** make sure your support staff pass the configured gates before upgrading, or they will lose access to the panel's Escalated pages.

## [1.2.1] - 2026-06-15

### Fixed
- **Satisfaction widget on the ticket view rendering the conversation thread.** The embedded `TicketConversation` and `SatisfactionRating` Livewire islands were mounted without a `key()`, so Livewire tracked both children under the same `null` slot in the parent page's `children` memo. When the parent re-rendered (e.g. switching relation-manager tabs) both placeholders resolved to the same previously-rendered child, swapping the satisfaction widget's content for the conversation thread. Each island now has a stable, record-scoped `wire:key`. (#35)
- CI: ignore the `filament/actions` advisory `PKSA-ndkp-2znf-9m7c` in the root `config.audit.ignore`. It flags every released Filament 5.x with no advisory-clean version, so Composer 2.9+ excluded the whole line and the `F^5.0` compatibility matrix could no longer resolve. Composer still caps at `<5.5.0` via the existing `conflict` block (root / test-matrix only; does not propagate to host apps).

## [1.2.0] - 2026-06-04

### Added
- **Newsletter admin panel.** Full Filament surface for the newsletter system, built on top of the base resources added in #30: `NewsletterResource` Send / Schedule / Test-send actions, a `ViewNewsletter` page, a `NewsletterSettings` page, a `MembersRelationManager` for list membership, and a `NewsletterOperations` support helper. Includes panel feature tests. (#39)
- Direct dependency on `escalated-dev/locale ^0.1` (central translations package). Already pulled in transitively via `escalated-laravel`, but pinned explicitly for clarity since Filament is a parallel admin surface.

### Changed
- Bumped `escalated-dev/escalated-laravel` to `^1.5.1` — the first Laravel release carrying the newsletter HTTP/service layer the panel drives. (#39)
- README: documented the translation resolution chain (app overrides → central `escalated-dev/locale` package → bundled `escalated-filament` fallbacks).

### Fixed
- Filament v3 `color()` compatibility on the newsletter resource tables/actions. (#39)
- CI: ignore Laravel 11.x security advisories in the root `config.audit.ignore` so the `L^11` leg of the compatibility matrix resolves under Composer 2.9+ (root / test-matrix only; does not propagate to host apps). (#39)

## [1.1.0] - 2026-04-18

### Added
- SideConversation relation manager for TicketResource
- Respect `escalated.ui.enabled` config gate
- 10 Filament resources + SSO/Email settings pages
- `show_powered_by` setting on Filament settings page
- Configurable Filament user fields and resources
- Docker dev/demo environment under `docker/` (excluded from the Composer dist). `docker compose up --build` boots a Postgres-backed Laravel + Filament 4 host with the plugin registered and a `/demo` click-to-login picker. (#21)

### Changed
- Widened version constraints for Laravel 13 and Testbench 11
- Updated escalated-laravel dependency to `^1.0`

### Fixed
- Migrate `ApiTokenResource` from Filament 3 to 4/5 API. The rest of the resource code already targeted v4/5 (`Filament\Schemas\Schema`), but `ApiTokenResource` still used v3-style `protected static ?string $navigationIcon`, blocking `php artisan package:discover`. (#23, fixes #22)
- Emit Postgres-compatible minute-diff SQL from `Reports.php`. Previous `selectRaw('AVG(TIMESTAMPDIFF(MINUTE, …))')` (MySQL-only) 500'd on Postgres. (#20)
- Blue-500 default for tag color picker (better dark mode contrast)
- Tiptap response handling
- Reply functionality
- Department resource relationship name
- Namespace imports and compatibility

## [v0.5.7] - Filament 4.x/5.x compatibility

### Added
- Filament 4.x and 5.x support with cross-version compatibility layer
- Multi-language (i18n) support with EN, ES, FR, DE translations
- Filament admin UI for plugin management
- Filament admin UI for API token management
- Pest test suite for escalated-filament
- GitHub Actions CI build pipeline
- Plugin system refactor with source column and composer delete guard

### Fixed
- Resolved all 123 test failures in Filament test suite
- Cross-version class aliases for Filament 5 Schema unification
- Replaced icon property declarations with getter methods for Filament 5
- Replaced static `$view` property with `getView()` for Filament 5
- Replaced static `$maxHeight` with `getMaxHeight()` for Filament 5
- Resolved Filament 5 `Tables\Actions` namespace
- Added compat aliases for `Filament\Forms\Components\Section` and `Filament\Resources\Components\Tab`
- Macro compatibility fixes
- Removed hardcoded version from composer.json

## [v0.5.0] - [v0.5.6]

### Added
- Complete Filament v3 plugin with full v0.4.0 feature parity
- Full feature documentation and setup guide

## [v0.4.0]

### Added
- Initial release of escalated-filament
- Filament admin panel integration for the Escalated support ticket system

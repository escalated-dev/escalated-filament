# Changelog

All notable changes to `escalated-filament` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Direct dependency on `escalated-dev/locale ^0.1` (central translations package). Already pulled in transitively via `escalated-laravel`, but pinned explicitly for clarity since Filament is a parallel admin surface.

### Changed
- README: documented the translation resolution chain (app overrides → central `escalated-dev/locale` package → bundled `escalated-filament` fallbacks).

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

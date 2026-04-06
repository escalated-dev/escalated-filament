# Changelog

All notable changes to `escalated-filament` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- SideConversation relation manager for TicketResource
- Respect `escalated.ui.enabled` config gate
- 10 Filament resources + SSO/Email settings pages
- `show_powered_by` setting on Filament settings page
- Configurable Filament user fields and resources

### Changed
- Widened version constraints for Laravel 13 and Testbench 11
- Updated escalated-laravel dependency to `^1.0`

### Fixed
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

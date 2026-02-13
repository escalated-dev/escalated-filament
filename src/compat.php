<?php

/**
 * Cross-version compatibility aliases for Filament 3/4/5.
 *
 * Filament 5 unified Form and Infolist into Filament\Schemas\Schema.
 * These aliases let v3-style code (use Filament\Forms\Form) work on v5.
 * On Filament 3/4 the original classes exist, so no aliases are created.
 */

if (! class_exists(\Filament\Forms\Form::class) && class_exists(\Filament\Schemas\Schema::class)) {
    class_alias(\Filament\Schemas\Schema::class, \Filament\Forms\Form::class);
}

if (! class_exists(\Filament\Infolists\Infolist::class) && class_exists(\Filament\Schemas\Schema::class)) {
    class_alias(\Filament\Schemas\Schema::class, \Filament\Infolists\Infolist::class);
}

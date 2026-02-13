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

// Resources\Components\Tab → Schemas\Components\Tabs\Tab (Filament 5)
if (! class_exists(\Filament\Resources\Components\Tab::class) && class_exists(\Filament\Schemas\Components\Tabs\Tab::class)) {
    class_alias(\Filament\Schemas\Components\Tabs\Tab::class, \Filament\Resources\Components\Tab::class);
}

// Tables\Actions → Actions (Filament 5 unified actions)
$tableActionAliases = [
    'Action',
    'EditAction',
    'DeleteAction',
    'ViewAction',
    'BulkAction',
    'BulkActionGroup',
    'DeleteBulkAction',
];

foreach ($tableActionAliases as $class) {
    $old = "Filament\\Tables\\Actions\\{$class}";
    $new = "Filament\\Actions\\{$class}";
    if (! class_exists($old) && class_exists($new)) {
        class_alias($new, $old);
    }
}

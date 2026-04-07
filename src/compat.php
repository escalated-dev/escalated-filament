<?php

use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Resources\Components\Tab;
use Filament\Schemas\Schema;

/**
 * Cross-version compatibility aliases for Filament 3/4/5.
 *
 * Filament 5 unified Form and Infolist into Filament\Schemas\Schema.
 * These aliases let v3-style code (use Filament\Forms\Form) work on v5.
 * On Filament 3/4 the original classes exist, so no aliases are created.
 */
if (! class_exists(Form::class) && class_exists(Schema::class)) {
    class_alias(Schema::class, Form::class);
}

if (! class_exists(Infolist::class) && class_exists(Schema::class)) {
    class_alias(Schema::class, Infolist::class);
}

// Forms\Components\Section → Schemas\Components\Section (Filament 5)
if (! class_exists(Section::class) && class_exists(Filament\Schemas\Components\Section::class)) {
    class_alias(Filament\Schemas\Components\Section::class, Section::class);
}

// Resources\Components\Tab → Schemas\Components\Tabs\Tab (Filament 5)
if (! class_exists(Tab::class) && class_exists(Filament\Schemas\Components\Tabs\Tab::class)) {
    class_alias(Filament\Schemas\Components\Tabs\Tab::class, Tab::class);
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

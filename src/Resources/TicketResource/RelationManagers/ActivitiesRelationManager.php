<?php

namespace Escalated\Filament\Resources\TicketResource\RelationManagers;

use Escalated\Laravel\Enums\ActivityType;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('escalated-filament::filament.resources.activities.title');
    }

    protected static ?string $icon = 'heroicon-o-clipboard-document-list';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label(__('escalated-filament::filament.resources.activities.column_activity'))
                    ->badge()
                    ->color(fn (ActivityType $state): string => match ($state) {
                        ActivityType::StatusChanged => 'info',
                        ActivityType::Assigned => 'primary',
                        ActivityType::Unassigned => 'gray',
                        ActivityType::PriorityChanged => 'warning',
                        ActivityType::TagAdded, ActivityType::TagRemoved => 'gray',
                        ActivityType::Escalated => 'danger',
                        ActivityType::SlaBreached => 'danger',
                        ActivityType::Replied => 'success',
                        ActivityType::NoteAdded => 'gray',
                        ActivityType::DepartmentChanged => 'info',
                        ActivityType::Reopened => 'warning',
                        ActivityType::Resolved => 'success',
                        ActivityType::Closed => 'gray',
                    })
                    ->formatStateUsing(fn (ActivityType $state) => $state->label()),

                Tables\Columns\TextColumn::make('causer.name')
                    ->label(__('escalated-filament::filament.resources.activities.column_by'))
                    ->default(__('escalated-filament::filament.resources.activities.default_system')),

                Tables\Columns\TextColumn::make('properties')
                    ->label(__('escalated-filament::filament.resources.activities.column_details'))
                    ->formatStateUsing(function ($state) {
                        if (! is_array($state)) {
                            return '-';
                        }

                        $parts = [];
                        foreach ($state as $key => $value) {
                            if (is_array($value)) {
                                $value = implode(', ', $value);
                            }
                            $parts[] = ucfirst(str_replace('_', ' ', $key)).': '.$value;
                        }

                        return implode(' | ', $parts) ?: '-';
                    })
                    ->wrap()
                    ->limit(100),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('escalated-filament::filament.resources.activities.column_date'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(collect(ActivityType::cases())->mapWithKeys(
                        fn (ActivityType $t) => [$t->value => $t->label()]
                    ))
                    ->multiple(),
            ]);
    }
}

<?php

namespace Escalated\Filament\Resources;

use Escalated\Filament\EscalatedFilamentPlugin;
use Escalated\Filament\Resources\SlaPolicyResource\Pages;
use Escalated\Laravel\Enums\TicketPriority;
use Escalated\Laravel\Models\SlaPolicy;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SlaPolicyResource extends Resource
{
    protected static ?string $model = SlaPolicy::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?int $navigationSort = 12;

    public static function getModelLabel(): string
    {
        return __('escalated-filament::filament.resources.sla_policy.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('escalated-filament::filament.resources.sla_policy.plural_model_label');
    }

    public static function getNavigationGroup(): ?string
    {
        return app(EscalatedFilamentPlugin::class)->getNavigationGroup();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('escalated-filament::filament.resources.sla_policy.section_policy_details'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->rows(2),

                        Forms\Components\Toggle::make('is_default')
                            ->label(__('escalated-filament::filament.resources.sla_policy.field_default_policy'))
                            ->helperText(__('escalated-filament::filament.resources.sla_policy.default_policy_helper')),

                        Forms\Components\Toggle::make('business_hours_only')
                            ->label(__('escalated-filament::filament.resources.sla_policy.field_business_hours'))
                            ->helperText(__('escalated-filament::filament.resources.sla_policy.business_hours_helper')),

                        Forms\Components\Toggle::make('is_active')
                            ->label(__('escalated-filament::filament.resources.sla_policy.field_active'))
                            ->default(true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('escalated-filament::filament.resources.sla_policy.section_first_response'))
                    ->description(__('escalated-filament::filament.resources.sla_policy.first_response_description'))
                    ->schema(
                        collect(TicketPriority::cases())->map(fn (TicketPriority $p) => Forms\Components\TextInput::make("first_response_hours.{$p->value}")
                            ->label($p->label())
                            ->numeric()
                            ->minValue(0)
                            ->step(0.5)
                            ->suffix('hours')
                        )->all()
                    )
                    ->columns(5),

                Forms\Components\Section::make(__('escalated-filament::filament.resources.sla_policy.section_resolution'))
                    ->description(__('escalated-filament::filament.resources.sla_policy.resolution_description'))
                    ->schema(
                        collect(TicketPriority::cases())->map(fn (TicketPriority $p) => Forms\Components\TextInput::make("resolution_hours.{$p->value}")
                            ->label($p->label())
                            ->numeric()
                            ->minValue(0)
                            ->step(0.5)
                            ->suffix('hours')
                        )->all()
                    )
                    ->columns(5),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->limit(40)
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_default')
                    ->label(__('escalated-filament::filament.resources.sla_policy.column_default'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('business_hours_only')
                    ->label(__('escalated-filament::filament.resources.sla_policy.column_business_hours'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('escalated-filament::filament.resources.sla_policy.column_active'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tickets_count')
                    ->label(__('escalated-filament::filament.resources.sla_policy.column_tickets'))
                    ->counts('tickets')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('escalated-filament::filament.resources.sla_policy.filter_active')),
                Tables\Filters\TernaryFilter::make('is_default')
                    ->label(__('escalated-filament::filament.resources.sla_policy.filter_default')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSlaPolicies::route('/'),
            'create' => Pages\CreateSlaPolicy::route('/create'),
            'edit' => Pages\EditSlaPolicy::route('/{record}/edit'),
        ];
    }
}

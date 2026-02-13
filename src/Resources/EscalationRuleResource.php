<?php

namespace Escalated\Filament\Resources;

use Escalated\Filament\EscalatedFilamentPlugin;
use Escalated\Filament\Resources\EscalationRuleResource\Pages;
use Escalated\Laravel\Enums\TicketPriority;
use Escalated\Laravel\Enums\TicketStatus;
use Escalated\Laravel\Escalated;
use Escalated\Laravel\Models\Department;
use Escalated\Laravel\Models\EscalationRule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EscalationRuleResource extends Resource
{
    protected static ?string $model = EscalationRule::class;

    protected static ?int $navigationSort = 13;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-arrow-trending-up';
    }

    public static function getNavigationGroup(): ?string
    {
        return app(EscalatedFilamentPlugin::class)->getNavigationGroup();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('escalated-filament::filament.resources.escalation_rule.section_rule_details'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->rows(2),

                        Forms\Components\TextInput::make('trigger_type')
                            ->required()
                            ->default('automatic')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        Forms\Components\Toggle::make('is_active')
                            ->label(__('escalated-filament::filament.resources.escalation_rule.field_active'))
                            ->default(true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('escalated-filament::filament.resources.escalation_rule.section_conditions'))
                    ->description(__('escalated-filament::filament.resources.escalation_rule.conditions_description'))
                    ->schema([
                        Forms\Components\Repeater::make('conditions')
                            ->schema([
                                Forms\Components\Select::make('field')
                                    ->options([
                                        'status' => __('escalated-filament::filament.resources.escalation_rule.condition_field_status'),
                                        'priority' => __('escalated-filament::filament.resources.escalation_rule.condition_field_priority'),
                                        'assigned' => __('escalated-filament::filament.resources.escalation_rule.condition_field_assignment'),
                                        'age_hours' => __('escalated-filament::filament.resources.escalation_rule.condition_field_age_hours'),
                                        'no_response_hours' => __('escalated-filament::filament.resources.escalation_rule.condition_field_no_response_hours'),
                                        'sla_breached' => __('escalated-filament::filament.resources.escalation_rule.condition_field_sla_breached'),
                                        'department_id' => __('escalated-filament::filament.resources.escalation_rule.condition_field_department'),
                                    ])
                                    ->required()
                                    ->live(),

                                Forms\Components\Select::make('value')
                                    ->options(fn (Forms\Get $get) => match ($get('field')) {
                                        'status' => collect(TicketStatus::cases())->mapWithKeys(
                                            fn (TicketStatus $s) => [$s->value => $s->label()]
                                        )->all(),
                                        'priority' => collect(TicketPriority::cases())->mapWithKeys(
                                            fn (TicketPriority $p) => [$p->value => $p->label()]
                                        )->all(),
                                        'assigned' => [
                                            'unassigned' => __('escalated-filament::filament.resources.escalation_rule.condition_value_unassigned'),
                                            'assigned' => __('escalated-filament::filament.resources.escalation_rule.condition_value_assigned'),
                                        ],
                                        'sla_breached' => ['true' => __('escalated-filament::filament.resources.escalation_rule.condition_value_yes')],
                                        'department_id' => Department::pluck('name', 'id')->all(),
                                        default => [],
                                    })
                                    ->visible(fn (Forms\Get $get) => in_array($get('field'), ['status', 'priority', 'assigned', 'sla_breached', 'department_id']))
                                    ->required(fn (Forms\Get $get) => in_array($get('field'), ['status', 'priority', 'assigned', 'sla_breached', 'department_id'])),

                                Forms\Components\TextInput::make('value')
                                    ->label(__('escalated-filament::filament.resources.escalation_rule.field_hours'))
                                    ->numeric()
                                    ->minValue(1)
                                    ->visible(fn (Forms\Get $get) => in_array($get('field'), ['age_hours', 'no_response_hours']))
                                    ->required(fn (Forms\Get $get) => in_array($get('field'), ['age_hours', 'no_response_hours'])),
                            ])
                            ->columns(3)
                            ->addActionLabel(__('escalated-filament::filament.resources.escalation_rule.add_condition'))
                            ->defaultItems(1)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make(__('escalated-filament::filament.resources.escalation_rule.section_actions'))
                    ->description(__('escalated-filament::filament.resources.escalation_rule.actions_description'))
                    ->schema([
                        Forms\Components\Repeater::make('actions')
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->options([
                                        'escalate' => __('escalated-filament::filament.resources.escalation_rule.action_escalate'),
                                        'change_priority' => __('escalated-filament::filament.resources.escalation_rule.action_change_priority'),
                                        'assign_to' => __('escalated-filament::filament.resources.escalation_rule.action_assign_to'),
                                        'change_department' => __('escalated-filament::filament.resources.escalation_rule.action_change_department'),
                                    ])
                                    ->required()
                                    ->live(),

                                Forms\Components\Select::make('value')
                                    ->options(fn (Forms\Get $get) => match ($get('type')) {
                                        'change_priority' => collect(TicketPriority::cases())->mapWithKeys(
                                            fn (TicketPriority $p) => [$p->value => $p->label()]
                                        )->all(),
                                        'assign_to' => app(Escalated::userModel())::pluck('name', 'id')->all(),
                                        'change_department' => Department::pluck('name', 'id')->all(),
                                        default => [],
                                    })
                                    ->visible(fn (Forms\Get $get) => $get('type') !== 'escalate' && $get('type') !== null)
                                    ->required(fn (Forms\Get $get) => $get('type') !== 'escalate' && $get('type') !== null),
                            ])
                            ->columns(2)
                            ->addActionLabel(__('escalated-filament::filament.resources.escalation_rule.add_action'))
                            ->defaultItems(1)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('order')
            ->defaultSort('order')
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->label(__('escalated-filament::filament.resources.escalation_rule.column_order'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->limit(40)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('trigger_type')
                    ->label(__('escalated-filament::filament.resources.escalation_rule.column_trigger'))
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('conditions')
                    ->label(__('escalated-filament::filament.resources.escalation_rule.column_conditions'))
                    ->formatStateUsing(fn ($state) => is_array($state) ? __('escalated-filament::filament.resources.escalation_rule.condition_count', ['count' => count($state)]) : __('escalated-filament::filament.resources.escalation_rule.zero_conditions'))
                    ->color('gray'),

                Tables\Columns\TextColumn::make('actions')
                    ->label(__('escalated-filament::filament.resources.escalation_rule.column_actions'))
                    ->formatStateUsing(fn ($state) => is_array($state) ? __('escalated-filament::filament.resources.escalation_rule.action_count', ['count' => count($state)]) : __('escalated-filament::filament.resources.escalation_rule.zero_actions'))
                    ->color('gray'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('escalated-filament::filament.resources.escalation_rule.column_active'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('escalated-filament::filament.resources.escalation_rule.filter_active')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('toggleActive')
                    ->label(fn (EscalationRule $record) => $record->is_active ? __('escalated-filament::filament.resources.escalation_rule.toggle_deactivate') : __('escalated-filament::filament.resources.escalation_rule.toggle_activate'))
                    ->icon(fn (EscalationRule $record) => $record->is_active ? 'heroicon-o-pause' : 'heroicon-o-play')
                    ->color(fn (EscalationRule $record) => $record->is_active ? 'warning' : 'success')
                    ->action(fn (EscalationRule $record) => $record->update(['is_active' => ! $record->is_active])),
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
            'index' => Pages\ListEscalationRules::route('/'),
            'create' => Pages\CreateEscalationRule::route('/create'),
            'edit' => Pages\EditEscalationRule::route('/{record}/edit'),
        ];
    }
}

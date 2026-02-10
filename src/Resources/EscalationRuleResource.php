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

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up';

    protected static ?int $navigationSort = 13;

    public static function getNavigationGroup(): ?string
    {
        return app(EscalatedFilamentPlugin::class)->getNavigationGroup();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Rule Details')
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
                            ->label('Active')
                            ->default(true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Conditions')
                    ->description('Define the conditions that must be met for this rule to trigger.')
                    ->schema([
                        Forms\Components\Repeater::make('conditions')
                            ->schema([
                                Forms\Components\Select::make('field')
                                    ->options([
                                        'status' => 'Status',
                                        'priority' => 'Priority',
                                        'assigned' => 'Assignment',
                                        'age_hours' => 'Age (hours)',
                                        'no_response_hours' => 'No Response (hours)',
                                        'sla_breached' => 'SLA Breached',
                                        'department_id' => 'Department',
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
                                        'assigned' => ['unassigned' => 'Unassigned', 'assigned' => 'Assigned'],
                                        'sla_breached' => ['true' => 'Yes'],
                                        'department_id' => Department::pluck('name', 'id')->all(),
                                        default => [],
                                    })
                                    ->visible(fn (Forms\Get $get) => in_array($get('field'), ['status', 'priority', 'assigned', 'sla_breached', 'department_id']))
                                    ->required(fn (Forms\Get $get) => in_array($get('field'), ['status', 'priority', 'assigned', 'sla_breached', 'department_id'])),

                                Forms\Components\TextInput::make('value')
                                    ->label('Hours')
                                    ->numeric()
                                    ->minValue(1)
                                    ->visible(fn (Forms\Get $get) => in_array($get('field'), ['age_hours', 'no_response_hours']))
                                    ->required(fn (Forms\Get $get) => in_array($get('field'), ['age_hours', 'no_response_hours'])),
                            ])
                            ->columns(3)
                            ->addActionLabel('Add Condition')
                            ->defaultItems(1)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Actions')
                    ->description('Define the actions to execute when conditions are met.')
                    ->schema([
                        Forms\Components\Repeater::make('actions')
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->options([
                                        'escalate' => 'Escalate Ticket',
                                        'change_priority' => 'Change Priority',
                                        'assign_to' => 'Assign To Agent',
                                        'change_department' => 'Change Department',
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
                            ->addActionLabel('Add Action')
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
                    ->label('#')
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->limit(40)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('trigger_type')
                    ->label('Trigger')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('conditions')
                    ->label('Conditions')
                    ->formatStateUsing(fn ($state) => is_array($state) ? count($state).' condition(s)' : '0 conditions')
                    ->color('gray'),

                Tables\Columns\TextColumn::make('actions')
                    ->label('Actions')
                    ->formatStateUsing(fn ($state) => is_array($state) ? count($state).' action(s)' : '0 actions')
                    ->color('gray'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('toggleActive')
                    ->label(fn (EscalationRule $record) => $record->is_active ? 'Deactivate' : 'Activate')
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

<?php

namespace Escalated\Filament\Resources;

use Escalated\Filament\EscalatedFilamentPlugin;
use Escalated\Filament\Resources\MacroResource\Pages;
use Escalated\Laravel\Enums\TicketPriority;
use Escalated\Laravel\Enums\TicketStatus;
use Escalated\Laravel\Escalated;
use Escalated\Laravel\Models\Department;
use Escalated\Laravel\Models\Macro;
use Escalated\Laravel\Models\Tag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MacroResource extends Resource
{
    protected static ?string $model = Macro::class;

    protected static ?int $navigationSort = 15;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-bolt';
    }

    public static function getNavigationGroup(): ?string
    {
        return app(EscalatedFilamentPlugin::class)->getNavigationGroup();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('escalated-filament::filament.resources.macro.section_macro_details'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('description')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        Forms\Components\Toggle::make('is_shared')
                            ->label(__('escalated-filament::filament.resources.macro.field_shared'))
                            ->helperText(__('escalated-filament::filament.resources.macro.shared_helper'))
                            ->default(true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('escalated-filament::filament.resources.macro.section_actions'))
                    ->description(__('escalated-filament::filament.resources.macro.actions_description'))
                    ->schema([
                        Forms\Components\Repeater::make('actions')
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->options([
                                        'status' => __('escalated-filament::filament.resources.macro.action_change_status'),
                                        'priority' => __('escalated-filament::filament.resources.macro.action_change_priority'),
                                        'assign' => __('escalated-filament::filament.resources.macro.action_assign_agent'),
                                        'tags' => __('escalated-filament::filament.resources.macro.action_add_tags'),
                                        'department' => __('escalated-filament::filament.resources.macro.action_change_department'),
                                        'reply' => __('escalated-filament::filament.resources.macro.action_add_reply'),
                                        'note' => __('escalated-filament::filament.resources.macro.action_add_note'),
                                    ])
                                    ->required()
                                    ->live(),

                                Forms\Components\Select::make('value')
                                    ->label(__('escalated-filament::filament.resources.macro.field_value'))
                                    ->options(fn (Forms\Get $get) => match ($get('type')) {
                                        'status' => collect(TicketStatus::cases())->mapWithKeys(
                                            fn (TicketStatus $s) => [$s->value => $s->label()]
                                        )->all(),
                                        'priority' => collect(TicketPriority::cases())->mapWithKeys(
                                            fn (TicketPriority $p) => [$p->value => $p->label()]
                                        )->all(),
                                        'assign' => app(Escalated::userModel())::pluck('name', 'id')->all(),
                                        'department' => Department::pluck('name', 'id')->all(),
                                        default => [],
                                    })
                                    ->visible(fn (Forms\Get $get) => in_array($get('type'), ['status', 'priority', 'assign', 'department']))
                                    ->required(fn (Forms\Get $get) => in_array($get('type'), ['status', 'priority', 'assign', 'department'])),

                                Forms\Components\Select::make('value')
                                    ->label(__('escalated-filament::filament.resources.macro.field_tags'))
                                    ->options(Tag::pluck('name', 'id'))
                                    ->multiple()
                                    ->visible(fn (Forms\Get $get) => $get('type') === 'tags')
                                    ->required(fn (Forms\Get $get) => $get('type') === 'tags'),

                                Forms\Components\RichEditor::make('value')
                                    ->label(__('escalated-filament::filament.resources.macro.field_message'))
                                    ->visible(fn (Forms\Get $get) => in_array($get('type'), ['reply', 'note']))
                                    ->required(fn (Forms\Get $get) => in_array($get('type'), ['reply', 'note'])),
                            ])
                            ->columns(2)
                            ->addActionLabel(__('escalated-filament::filament.resources.macro.add_action'))
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
                    ->label(__('escalated-filament::filament.resources.macro.column_order'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->limit(40)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('actions')
                    ->label(__('escalated-filament::filament.resources.macro.column_actions'))
                    ->formatStateUsing(fn ($state) => is_array($state) ? __('escalated-filament::filament.resources.macro.action_count', ['count' => count($state)]) : __('escalated-filament::filament.resources.macro.zero_actions'))
                    ->color('gray'),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label(__('escalated-filament::filament.resources.macro.column_created_by'))
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_shared')
                    ->label(__('escalated-filament::filament.resources.macro.column_shared'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_shared')
                    ->label(__('escalated-filament::filament.resources.macro.filter_shared')),
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
            'index' => Pages\ListMacros::route('/'),
            'create' => Pages\CreateMacro::route('/create'),
            'edit' => Pages\EditMacro::route('/{record}/edit'),
        ];
    }
}

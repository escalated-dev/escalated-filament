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

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?int $navigationSort = 15;

    public static function getNavigationGroup(): ?string
    {
        return app(EscalatedFilamentPlugin::class)->getNavigationGroup();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Macro Details')
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
                            ->label('Shared')
                            ->helperText('Shared macros are visible to all agents.')
                            ->default(true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Actions')
                    ->description('Define the actions this macro will perform when applied to a ticket.')
                    ->schema([
                        Forms\Components\Repeater::make('actions')
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->options([
                                        'status' => 'Change Status',
                                        'priority' => 'Change Priority',
                                        'assign' => 'Assign Agent',
                                        'tags' => 'Add Tags',
                                        'department' => 'Change Department',
                                        'reply' => 'Add Reply',
                                        'note' => 'Add Internal Note',
                                    ])
                                    ->required()
                                    ->live(),

                                Forms\Components\Select::make('value')
                                    ->label('Value')
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
                                    ->label('Tags')
                                    ->options(Tag::pluck('name', 'id'))
                                    ->multiple()
                                    ->visible(fn (Forms\Get $get) => $get('type') === 'tags')
                                    ->required(fn (Forms\Get $get) => $get('type') === 'tags'),

                                Forms\Components\RichEditor::make('value')
                                    ->label('Message')
                                    ->visible(fn (Forms\Get $get) => in_array($get('type'), ['reply', 'note']))
                                    ->required(fn (Forms\Get $get) => in_array($get('type'), ['reply', 'note'])),
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

                Tables\Columns\TextColumn::make('actions')
                    ->label('Actions')
                    ->formatStateUsing(fn ($state) => is_array($state) ? count($state).' action(s)' : '0 actions')
                    ->color('gray'),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Created By')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_shared')
                    ->label('Shared')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_shared')
                    ->label('Shared'),
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

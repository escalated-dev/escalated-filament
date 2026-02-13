<?php

namespace Escalated\Filament\Resources;

use Escalated\Filament\EscalatedFilamentPlugin;
use Escalated\Filament\Resources\DepartmentResource\Pages;
use Escalated\Laravel\Escalated;
use Escalated\Laravel\Models\Department;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static ?int $navigationSort = 10;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-building-office';
    }

    public static function getNavigationGroup(): ?string
    {
        return app(EscalatedFilamentPlugin::class)->getNavigationGroup();
    }

    public static function getNavigationLabel(): string
    {
        return __('escalated-filament::filament.resources.department.navigation_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set('slug', \Illuminate\Support\Str::slug($state ?? ''))),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_active')
                            ->label(__('escalated-filament::filament.resources.department.field_active'))
                            ->default(true),

                        Forms\Components\Select::make('agents')
                            ->label(__('escalated-filament::filament.resources.department.field_agents'))
                            ->relationship('agents', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->sortable()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('escalated-filament::filament.resources.department.column_active'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('agents_count')
                    ->label(__('escalated-filament::filament.resources.department.column_agents'))
                    ->counts('agents')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tickets_count')
                    ->label(__('escalated-filament::filament.resources.department.column_tickets'))
                    ->counts('tickets')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('escalated-filament::filament.resources.department.filter_active')),
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDepartments::route('/'),
            'create' => Pages\CreateDepartment::route('/create'),
            'edit' => Pages\EditDepartment::route('/{record}/edit'),
        ];
    }
}

<?php

namespace Escalated\Filament\Resources;

use Escalated\Filament\EscalatedFilamentPlugin;
use Escalated\Filament\Resources\BusinessScheduleResource\Pages;
use Escalated\Laravel\Models\BusinessSchedule;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class BusinessScheduleResource extends Resource
{
    protected static ?string $model = BusinessSchedule::class;

    protected static ?int $navigationSort = 26;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-clock';
    }

    public static function getNavigationGroup(): ?string
    {
        return app(EscalatedFilamentPlugin::class)->getNavigationGroup();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('timezone')
                            ->options([
                                'UTC' => 'UTC',
                                'America/New_York' => 'America/New_York',
                                'America/Chicago' => 'America/Chicago',
                                'America/Denver' => 'America/Denver',
                                'America/Los_Angeles' => 'America/Los_Angeles',
                                'America/Toronto' => 'America/Toronto',
                                'America/Sao_Paulo' => 'America/Sao_Paulo',
                                'Europe/London' => 'Europe/London',
                                'Europe/Paris' => 'Europe/Paris',
                                'Europe/Berlin' => 'Europe/Berlin',
                                'Europe/Amsterdam' => 'Europe/Amsterdam',
                                'Asia/Tokyo' => 'Asia/Tokyo',
                                'Asia/Shanghai' => 'Asia/Shanghai',
                                'Asia/Kolkata' => 'Asia/Kolkata',
                                'Asia/Dubai' => 'Asia/Dubai',
                                'Asia/Singapore' => 'Asia/Singapore',
                                'Australia/Sydney' => 'Australia/Sydney',
                                'Pacific/Auckland' => 'Pacific/Auckland',
                            ])
                            ->required()
                            ->searchable(),

                        Forms\Components\Toggle::make('is_default')
                            ->default(false),
                    ])
                    ->columns(2),

                ...collect(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])
                    ->map(fn (string $day) => Forms\Components\Section::make(ucfirst($day))
                        ->schema([
                            Forms\Components\TimePicker::make("{$day}_start")
                                ->label('Start')
                                ->seconds(false),

                            Forms\Components\TimePicker::make("{$day}_end")
                                ->label('End')
                                ->seconds(false),
                        ])
                        ->columns(2)
                        ->collapsible()
                    )->all(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('timezone')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_default')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListBusinessSchedules::route('/'),
            'create' => Pages\CreateBusinessSchedule::route('/create'),
            'edit' => Pages\EditBusinessSchedule::route('/{record}/edit'),
        ];
    }
}

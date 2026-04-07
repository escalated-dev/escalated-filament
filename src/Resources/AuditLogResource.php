<?php

namespace Escalated\Filament\Resources;

use Escalated\Filament\EscalatedFilamentPlugin;
use Escalated\Filament\Resources\AuditLogResource\Pages;
use Escalated\Laravel\Models\AuditLog;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static ?int $navigationSort = 30;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-clipboard-document-list';
    }

    public static function getNavigationGroup(): ?string
    {
        return app(EscalatedFilamentPlugin::class)->getNavigationGroup();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('action')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('auditable_type')
                    ->label('Entity Type')
                    ->sortable(),

                Tables\Columns\TextColumn::make('auditable_id')
                    ->label('Entity ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('changes')
                    ->label('Changes')
                    ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT) : $state)
                    ->limit(60)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditLogs::route('/'),
        ];
    }
}

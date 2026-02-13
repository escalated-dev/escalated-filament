<?php

namespace Escalated\Filament\Resources;

use Escalated\Filament\EscalatedFilamentPlugin;
use Escalated\Filament\Resources\CannedResponseResource\Pages;
use Escalated\Laravel\Models\CannedResponse;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CannedResponseResource extends Resource
{
    protected static ?string $model = CannedResponse::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 14;

    public static function getNavigationGroup(): ?string
    {
        return app(EscalatedFilamentPlugin::class)->getNavigationGroup();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('category')
                            ->maxLength(255)
                            ->datalist(
                                CannedResponse::whereNotNull('category')
                                    ->distinct()
                                    ->pluck('category')
                                    ->all()
                            ),

                        Forms\Components\RichEditor::make('body')
                            ->label(__('escalated-filament::filament.resources.canned_response.field_response_body'))
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_shared')
                            ->label(__('escalated-filament::filament.resources.canned_response.field_shared'))
                            ->helperText(__('escalated-filament::filament.resources.canned_response.shared_helper'))
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('body')
                    ->html()
                    ->limit(60)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label(__('escalated-filament::filament.resources.canned_response.column_created_by'))
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_shared')
                    ->label(__('escalated-filament::filament.resources.canned_response.field_shared'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_shared')
                    ->label(__('escalated-filament::filament.resources.canned_response.field_shared')),
                Tables\Filters\SelectFilter::make('category')
                    ->options(
                        CannedResponse::whereNotNull('category')
                            ->distinct()
                            ->pluck('category', 'category')
                            ->all()
                    ),
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
            'index' => Pages\ListCannedResponses::route('/'),
            'create' => Pages\CreateCannedResponse::route('/create'),
            'edit' => Pages\EditCannedResponse::route('/{record}/edit'),
        ];
    }
}

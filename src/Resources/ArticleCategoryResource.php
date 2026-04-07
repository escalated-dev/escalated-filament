<?php

namespace Escalated\Filament\Resources;

use Escalated\Filament\EscalatedFilamentPlugin;
use Escalated\Filament\Resources\ArticleCategoryResource\Pages;
use Escalated\Laravel\Models\ArticleCategory;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ArticleCategoryResource extends Resource
{
    protected static ?string $model = ArticleCategory::class;

    protected static ?int $navigationSort = 28;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-folder';
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
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('parent_id')
                            ->label('Parent Category')
                            ->options(fn (?ArticleCategory $record) => ArticleCategory::query()
                                ->when($record, fn ($query) => $query->where('id', '!=', $record->id))
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->nullable(),

                        Forms\Components\TextInput::make('order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
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
                    ->sortable(),

                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Parent')
                    ->placeholder('None')
                    ->sortable(),

                Tables\Columns\TextColumn::make('articles_count')
                    ->label('Articles')
                    ->counts('articles')
                    ->sortable(),

                Tables\Columns\TextColumn::make('order')
                    ->sortable(),
            ])
            ->defaultSort('order')
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
            'index' => Pages\ListArticleCategories::route('/'),
            'create' => Pages\CreateArticleCategory::route('/create'),
            'edit' => Pages\EditArticleCategory::route('/{record}/edit'),
        ];
    }
}

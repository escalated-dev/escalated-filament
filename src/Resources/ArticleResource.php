<?php

namespace Escalated\Filament\Resources;

use Escalated\Filament\EscalatedFilamentPlugin;
use Escalated\Filament\Resources\ArticleResource\Pages;
use Escalated\Laravel\Models\Article;
use Escalated\Laravel\Models\ArticleCategory;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?int $navigationSort = 27;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-document-text';
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
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\Select::make('category_id')
                            ->label('Category')
                            ->options(ArticleCategory::pluck('name', 'id'))
                            ->searchable()
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                            ])
                            ->required()
                            ->default('draft'),

                        Forms\Components\RichEditor::make('body')
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Hidden::make('author_id')
                            ->default(fn () => auth()->id()),
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

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'published' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('views_count')
                    ->label('Views')
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
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}

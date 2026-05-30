<?php

namespace Escalated\Filament\Resources;

use Escalated\Filament\EscalatedFilamentPlugin;
use Escalated\Filament\Resources\NewsletterTemplateResource\Pages;
use Escalated\Laravel\Models\Newsletter\NewsletterTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NewsletterTemplateResource extends Resource
{
    protected static ?string $model = NewsletterTemplate::class;

    protected static ?int $navigationSort = 27;

    protected static ?string $navigationLabel = 'Newsletter templates';

    protected static ?string $modelLabel = 'Newsletter template';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-document-text';
    }

    public static function getNavigationGroup(): ?string
    {
        return app(EscalatedFilamentPlugin::class)->getNavigationGroup();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return (bool) config('escalated.enable_newsletters', false);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required()->maxLength(255),
            Forms\Components\TextInput::make('theme')
                ->default(fn () => config('escalated.newsletters.default_theme', 'default'))
                ->maxLength(64),
            Forms\Components\TextInput::make('subject_template')->maxLength(998),
            Forms\Components\Textarea::make('body_markdown')
                ->required()
                ->rows(20)
                ->columnSpanFull()
                ->placeholder('# Hello {{ contact.first_name }}\n\nWrite your template in Markdown.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('theme'),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNewsletterTemplates::route('/'),
            'create' => Pages\CreateNewsletterTemplate::route('/create'),
            'edit' => Pages\EditNewsletterTemplate::route('/{record}/edit'),
        ];
    }
}

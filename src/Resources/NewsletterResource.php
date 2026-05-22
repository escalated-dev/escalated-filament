<?php

namespace Escalated\Filament\Resources;

use Escalated\Filament\EscalatedFilamentPlugin;
use Escalated\Filament\Resources\NewsletterResource\Pages;
use Escalated\Laravel\Models\Newsletter\Newsletter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NewsletterResource extends Resource
{
    protected static ?string $model = Newsletter::class;

    protected static ?int $navigationSort = 25;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-envelope';
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
            Forms\Components\Section::make('Newsletter')->schema([
                Forms\Components\TextInput::make('subject')
                    ->required()
                    ->maxLength(998),
                Forms\Components\TextInput::make('from_email')
                    ->email()
                    ->required()
                    ->maxLength(320),
                Forms\Components\TextInput::make('from_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('reply_to')
                    ->email()
                    ->maxLength(320),
                Forms\Components\Select::make('target_list_id')
                    ->relationship('targetList', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('template_id')
                    ->relationship('template', 'name')
                    ->searchable(),
                Forms\Components\TextInput::make('theme')
                    ->default(fn () => config('escalated.newsletters.default_theme', 'default'))
                    ->maxLength(64),
                Forms\Components\Textarea::make('body_markdown')
                    ->rows(20)
                    ->columnSpanFull()
                    ->placeholder('# Hello {{ contact.first_name }}\n\nWrite your newsletter body in Markdown.'),
                Forms\Components\Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'scheduled' => 'Scheduled',
                    ])
                    ->default('draft')
                    ->required(),
                Forms\Components\DateTimePicker::make('scheduled_at')
                    ->visible(fn (Forms\Get $get) => $get('status') === 'scheduled'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subject')->searchable()->limit(60),
                Tables\Columns\TextColumn::make('targetList.name')->label('List'),
                Tables\Columns\TextColumn::make('status')->badge()->colors([
                    'gray' => 'draft',
                    'info' => fn ($state) => in_array($state, ['scheduled', 'sending'], true),
                    'success' => 'sent',
                    'warning' => 'paused',
                    'danger' => 'failed',
                ]),
                Tables\Columns\TextColumn::make('scheduled_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('sent_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('summary_total')->label('Recipients'),
                Tables\Columns\TextColumn::make('summary_opened')->label('Opened'),
                Tables\Columns\TextColumn::make('summary_clicked')->label('Clicked'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'draft' => 'Draft',
                    'scheduled' => 'Scheduled',
                    'sending' => 'Sending',
                    'sent' => 'Sent',
                    'paused' => 'Paused',
                    'failed' => 'Failed',
                ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => in_array($record->status, ['draft', 'scheduled'], true)),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn ($record) => $record->status === 'draft'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNewsletters::route('/'),
            'create' => Pages\CreateNewsletter::route('/create'),
            'edit' => Pages\EditNewsletter::route('/{record}/edit'),
        ];
    }
}

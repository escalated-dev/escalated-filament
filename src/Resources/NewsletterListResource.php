<?php

namespace Escalated\Filament\Resources;

use Escalated\Filament\EscalatedFilamentPlugin;
use Escalated\Filament\Resources\NewsletterListResource\Pages;
use Escalated\Laravel\Models\Newsletter\NewsletterList;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NewsletterListResource extends Resource
{
    protected static ?string $model = NewsletterList::class;

    protected static ?int $navigationSort = 26;

    protected static ?string $navigationLabel = 'Newsletter lists';

    protected static ?string $modelLabel = 'Newsletter list';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-user-group';
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
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\Textarea::make('description')->rows(2)->columnSpanFull(),
            Forms\Components\Select::make('kind')
                ->options(['static' => 'Static', 'dynamic' => 'Dynamic'])
                ->required()
                ->default('static')
                ->reactive(),
            Forms\Components\Textarea::make('filter_json')
                ->label('Filter rules (JSON)')
                ->rows(8)
                ->visible(fn (Forms\Get $get) => $get('kind') === 'dynamic')
                ->afterStateHydrated(function ($component, $state) {
                    $component->state(is_array($state) ? json_encode($state, JSON_PRETTY_PRINT) : (string) $state);
                })
                ->dehydrateStateUsing(function ($state) {
                    if (is_string($state)) {
                        try {
                            return json_decode($state, true, 512, JSON_THROW_ON_ERROR);
                        } catch (\JsonException) {
                            return ['rules' => []];
                        }
                    }
                    return $state;
                }),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('kind')->badge(),
                Tables\Columns\TextColumn::make('members_count')->counts('members')->label('Members'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNewsletterLists::route('/'),
            'create' => Pages\CreateNewsletterList::route('/create'),
            'edit' => Pages\EditNewsletterList::route('/{record}/edit'),
        ];
    }
}

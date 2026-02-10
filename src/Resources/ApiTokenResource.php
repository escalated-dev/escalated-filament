<?php

namespace Escalated\Filament\Resources;

use Escalated\Filament\EscalatedFilamentPlugin;
use Escalated\Filament\Resources\ApiTokenResource\Pages;
use Escalated\Laravel\Escalated;
use Escalated\Laravel\Models\ApiToken;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;

class ApiTokenResource extends Resource
{
    protected static ?string $model = ApiToken::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?int $navigationSort = 16;

    protected static ?string $modelLabel = 'API Token';

    protected static ?string $pluralModelLabel = 'API Tokens';

    public static function getNavigationGroup(): ?string
    {
        return app(EscalatedFilamentPlugin::class)->getNavigationGroup();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return config('escalated.api.enabled', false);
    }

    public static function form(Form $form): Form
    {
        $agentGate = config('escalated.authorization.agent_gate', 'escalated-agent');

        return $form
            ->schema([
                Forms\Components\Section::make('Token Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->helperText('A descriptive name for this token (e.g., "Desktop App", "CI Pipeline").'),

                        Forms\Components\Select::make('tokenable_id')
                            ->label('User')
                            ->options(function () use ($agentGate) {
                                $userModel = Escalated::newUserModel();

                                return $userModel->newQuery()->get()
                                    ->filter(fn ($user) => Gate::forUser($user)->allows($agentGate))
                                    ->mapWithKeys(fn ($user) => [$user->getKey() => "{$user->name} ({$user->email})"])
                                    ->all();
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText('The user this token will authenticate as.')
                            ->visibleOn('create'),

                        Forms\Components\CheckboxList::make('abilities')
                            ->options([
                                'agent' => 'Agent - Access agent-level endpoints (tickets, replies, assignments)',
                                'admin' => 'Admin - Access admin-level endpoints (settings, reports)',
                                '*' => 'Wildcard - Full access to all endpoints',
                            ])
                            ->required()
                            ->columns(1)
                            ->helperText('Select the permissions this token should have.'),

                        Forms\Components\TextInput::make('expires_in_days')
                            ->label('Expires In')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(365)
                            ->suffix('days')
                            ->nullable()
                            ->helperText('Leave empty for a non-expiring token.')
                            ->visibleOn('create'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('tokenable.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->description(fn (ApiToken $record) => $record->tokenable?->email),

                Tables\Columns\TextColumn::make('abilities')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '*' => 'danger',
                        'admin' => 'warning',
                        'agent' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        '*' => 'Wildcard',
                        default => ucfirst($state),
                    }),

                Tables\Columns\TextColumn::make('last_used_at')
                    ->label('Last Used')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never')
                    ->description(fn (ApiToken $record) => $record->last_used_ip)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never')
                    ->color(fn (ApiToken $record) => $record->isExpired() ? 'danger' : null),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->state(fn (ApiToken $record): bool => ! $record->isExpired())
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(query: function ($query, string $direction) {
                        return $query->orderByRaw(
                            "CASE WHEN expires_at IS NULL OR expires_at > NOW() THEN 0 ELSE 1 END {$direction}"
                        );
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'expired' => 'Expired',
                    ])
                    ->query(function ($query, array $data) {
                        return match ($data['value'] ?? null) {
                            'active' => $query->active(),
                            'expired' => $query->expired(),
                            default => $query,
                        };
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->label('Revoke')
                    ->modalHeading('Revoke API Token')
                    ->modalDescription('Are you sure you want to revoke this token? Any applications using this token will immediately lose access.')
                    ->modalSubmitActionLabel('Revoke Token'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Revoke Selected')
                        ->modalHeading('Revoke API Tokens')
                        ->modalDescription('Are you sure you want to revoke the selected tokens? Any applications using these tokens will immediately lose access.'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApiTokens::route('/'),
            'create' => Pages\CreateApiToken::route('/create'),
            'edit' => Pages\EditApiToken::route('/{record}/edit'),
        ];
    }
}

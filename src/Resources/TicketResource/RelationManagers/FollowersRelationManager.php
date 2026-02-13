<?php

namespace Escalated\Filament\Resources\TicketResource\RelationManagers;

use Escalated\Laravel\Escalated;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class FollowersRelationManager extends RelationManager
{
    protected static string $relationship = 'followers';

    public static function getIcon(): ?string
    {
        return 'heroicon-o-bell';
    }

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('escalated-filament::filament.resources.followers.title');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->disabled(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('escalated-filament::filament.resources.followers.column_name'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label(__('escalated-filament::filament.resources.followers.column_email'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('pivot.created_at')
                    ->label(__('escalated-filament::filament.resources.followers.column_following_since'))
                    ->dateTime(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('addFollower')
                    ->label(__('escalated-filament::filament.resources.followers.action_add_follower'))
                    ->icon('heroicon-o-user-plus')
                    ->form([
                        Forms\Components\Select::make('user_id')
                            ->label(__('escalated-filament::filament.resources.followers.field_user'))
                            ->options(fn () => app(Escalated::userModel())::pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        $this->getOwnerRecord()->follow($data['user_id']);
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('remove')
                    ->label(__('escalated-filament::filament.resources.followers.action_remove'))
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function ($record): void {
                        $this->getOwnerRecord()->unfollow($record->getKey());
                    }),
            ]);
    }
}

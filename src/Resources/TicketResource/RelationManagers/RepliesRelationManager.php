<?php

namespace Escalated\Filament\Resources\TicketResource\RelationManagers;

use Escalated\Laravel\Models\Reply;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RepliesRelationManager extends RelationManager
{
    protected static string $relationship = 'replies';

    public static function getIcon(): ?string
    {
        return 'heroicon-o-chat-bubble-left-right';
    }

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('escalated-filament::filament.resources.replies.title');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\RichEditor::make('body')
                    ->label(__('escalated-filament::filament.resources.replies.field_message'))
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('is_internal_note')
                    ->label(__('escalated-filament::filament.resources.replies.field_internal_note'))
                    ->helperText(__('escalated-filament::filament.resources.replies.internal_note_helper'))
                    ->default(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('body')
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('author.name')
                    ->label(__('escalated-filament::filament.resources.replies.column_author'))
                    ->default(__('escalated-filament::filament.resources.replies.default_system')),

                Tables\Columns\TextColumn::make('body')
                    ->label(__('escalated-filament::filament.resources.replies.column_message'))
                    ->html()
                    ->limit(100)
                    ->wrap(),

                Tables\Columns\IconColumn::make('is_internal_note')
                    ->label(__('escalated-filament::filament.resources.replies.column_internal'))
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-lock-open')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                Tables\Columns\IconColumn::make('is_pinned')
                    ->label(__('escalated-filament::filament.resources.replies.column_pinned'))
                    ->boolean()
                    ->trueIcon('heroicon-s-bookmark')
                    ->falseIcon('heroicon-o-bookmark')
                    ->trueColor('primary')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('escalated-filament::filament.resources.replies.column_date'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_internal_note')
                    ->label(__('escalated-filament::filament.resources.replies.filter_type'))
                    ->placeholder(__('escalated-filament::filament.resources.replies.filter_all'))
                    ->trueLabel(__('escalated-filament::filament.resources.replies.filter_internal_only'))
                    ->falseLabel(__('escalated-filament::filament.resources.replies.filter_public_only')),

                Tables\Filters\TernaryFilter::make('is_pinned')
                    ->label(__('escalated-filament::filament.resources.replies.filter_pinned'))
                    ->placeholder(__('escalated-filament::filament.resources.replies.filter_all'))
                    ->trueLabel(__('escalated-filament::filament.resources.replies.filter_pinned_only'))
                    ->falseLabel(__('escalated-filament::filament.resources.replies.filter_not_pinned')),
            ])
            ->headerActions([
                Tables\Actions\Action::make('reply')
                    ->label(__('escalated-filament::filament.resources.replies.action_add_reply'))
                    ->icon('heroicon-o-chat-bubble-left')
                    ->form([
                        Forms\Components\RichEditor::make('body')
                            ->label(__('escalated-filament::filament.resources.replies.field_reply'))
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->action(function (array $data): void {
                        app(\Escalated\Laravel\Services\TicketService::class)
                            ->reply($this->getOwnerRecord(), auth()->user(), $data['body']);
                    }),

                Tables\Actions\Action::make('note')
                    ->label(__('escalated-filament::filament.resources.replies.action_add_note'))
                    ->icon('heroicon-o-pencil-square')
                    ->color('gray')
                    ->form([
                        Forms\Components\RichEditor::make('body')
                            ->label(__('escalated-filament::filament.resources.replies.field_internal_note'))
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->action(function (array $data): void {
                        app(\Escalated\Laravel\Services\TicketService::class)
                            ->addNote($this->getOwnerRecord(), auth()->user(), $data['body']);
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('togglePin')
                    ->label(fn (Reply $record) => $record->is_pinned ? __('escalated-filament::filament.resources.replies.action_unpin') : __('escalated-filament::filament.resources.replies.action_pin'))
                    ->icon(fn (Reply $record) => $record->is_pinned ? 'heroicon-s-bookmark' : 'heroicon-o-bookmark')
                    ->action(fn (Reply $record) => $record->update(['is_pinned' => ! $record->is_pinned]))
                    ->visible(fn (Reply $record) => $record->is_internal_note),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

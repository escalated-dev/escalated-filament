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

    protected static ?string $title = 'Replies & Notes';

    protected static ?string $icon = 'heroicon-o-chat-bubble-left-right';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\RichEditor::make('body')
                    ->label('Message')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('is_internal_note')
                    ->label('Internal Note')
                    ->helperText('Internal notes are only visible to agents.')
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
                    ->label('Author')
                    ->default('System'),

                Tables\Columns\TextColumn::make('body')
                    ->label('Message')
                    ->html()
                    ->limit(100)
                    ->wrap(),

                Tables\Columns\IconColumn::make('is_internal_note')
                    ->label('Internal')
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-lock-open')
                    ->trueColor('warning')
                    ->falseColor('gray'),

                Tables\Columns\IconColumn::make('is_pinned')
                    ->label('Pinned')
                    ->boolean()
                    ->trueIcon('heroicon-s-bookmark')
                    ->falseIcon('heroicon-o-bookmark')
                    ->trueColor('primary')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_internal_note')
                    ->label('Type')
                    ->placeholder('All')
                    ->trueLabel('Internal Notes Only')
                    ->falseLabel('Public Replies Only'),

                Tables\Filters\TernaryFilter::make('is_pinned')
                    ->label('Pinned')
                    ->placeholder('All')
                    ->trueLabel('Pinned Only')
                    ->falseLabel('Not Pinned'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('reply')
                    ->label('Add Reply')
                    ->icon('heroicon-o-chat-bubble-left')
                    ->form([
                        Forms\Components\RichEditor::make('body')
                            ->label('Reply')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->action(function (array $data): void {
                        app(\Escalated\Laravel\Services\TicketService::class)
                            ->reply($this->getOwnerRecord(), auth()->user(), $data['body']);
                    }),

                Tables\Actions\Action::make('note')
                    ->label('Add Note')
                    ->icon('heroicon-o-pencil-square')
                    ->color('gray')
                    ->form([
                        Forms\Components\RichEditor::make('body')
                            ->label('Internal Note')
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
                    ->label(fn (Reply $record) => $record->is_pinned ? 'Unpin' : 'Pin')
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

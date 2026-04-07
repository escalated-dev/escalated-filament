<?php

namespace Escalated\Filament\Resources\TicketResource\RelationManagers;

use Escalated\Laravel\Escalated;
use Escalated\Laravel\Models\SideConversation;
use Escalated\Laravel\Models\SideConversationReply;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SideConversationsRelationManager extends RelationManager
{
    protected static string $relationship = 'sideConversations';

    public static function getIcon(Model $ownerRecord, string $pageClass): ?string
    {
        return 'heroicon-o-chat-bubble-left-ellipsis';
    }

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return 'Side Conversations';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('subject')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Forms\Components\RichEditor::make('body')
                    ->label('Initial Message')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\Select::make('channel')
                    ->options([
                        'internal' => 'Internal',
                        'email' => 'Email',
                    ])
                    ->default('internal')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('subject')
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(fn (SideConversation $record) => $record->subject),

                Tables\Columns\TextColumn::make('creator.'.Escalated::userDisplayColumn())
                    ->label('Created By')
                    ->sortable()
                    ->default('System'),

                Tables\Columns\TextColumn::make('channel')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'internal' => 'gray',
                        'email' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open' => 'success',
                        'closed' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('replies_count')
                    ->label('Replies')
                    ->counts('replies')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'open' => 'Open',
                        'closed' => 'Closed',
                    ]),

                Tables\Filters\SelectFilter::make('channel')
                    ->options([
                        'internal' => 'Internal',
                        'email' => 'Email',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('create')
                    ->label('New Side Conversation')
                    ->icon('heroicon-o-plus')
                    ->form([
                        Forms\Components\TextInput::make('subject')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('body')
                            ->label('Initial Message')
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Select::make('channel')
                            ->options([
                                'internal' => 'Internal',
                                'email' => 'Email',
                            ])
                            ->default('internal')
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        $conversation = $this->getOwnerRecord()->sideConversations()->create([
                            'subject' => $data['subject'],
                            'channel' => $data['channel'],
                            'status' => 'open',
                            'created_by' => auth()->id(),
                        ]);

                        SideConversationReply::create([
                            'side_conversation_id' => $conversation->id,
                            'body' => $data['body'],
                            'author_id' => auth()->id(),
                        ]);
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading(fn (SideConversation $record) => $record->subject)
                    ->modalContent(function (SideConversation $record) {
                        $replies = $record->replies()->with('author')->oldest()->get();

                        return view('escalated-filament::side-conversation-replies', [
                            'replies' => $replies,
                        ]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),

                Tables\Actions\Action::make('reply')
                    ->label('Reply')
                    ->icon('heroicon-o-chat-bubble-left')
                    ->color('primary')
                    ->form([
                        Forms\Components\RichEditor::make('body')
                            ->label('Reply')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->action(function (SideConversation $record, array $data): void {
                        SideConversationReply::create([
                            'side_conversation_id' => $record->id,
                            'body' => $data['body'],
                            'author_id' => auth()->id(),
                        ]);
                    })
                    ->visible(fn (SideConversation $record) => $record->status === 'open'),

                Tables\Actions\Action::make('close')
                    ->label('Close')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (SideConversation $record) => $record->update(['status' => 'closed']))
                    ->visible(fn (SideConversation $record) => $record->status === 'open'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

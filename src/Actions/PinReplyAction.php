<?php

namespace Escalated\Filament\Actions;

use Escalated\Laravel\Models\Reply;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;

class PinReplyAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'pinReply';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(fn (Reply $record) => $record->is_pinned ? 'Unpin' : 'Pin')
            ->icon(fn (Reply $record) => $record->is_pinned ? 'heroicon-s-bookmark' : 'heroicon-o-bookmark')
            ->color(fn (Reply $record) => $record->is_pinned ? 'primary' : 'gray')
            ->action(function (Reply $record): void {
                $record->update(['is_pinned' => ! $record->is_pinned]);

                $message = $record->is_pinned ? 'Reply pinned' : 'Reply unpinned';

                Notification::make()
                    ->title($message)
                    ->success()
                    ->send();
            })
            ->visible(fn (Reply $record) => $record->is_internal_note);
    }
}

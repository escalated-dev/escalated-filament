<?php

namespace Escalated\Filament\Actions;

use Escalated\Laravel\Models\Ticket;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;

class FollowTicketAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'followTicket';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(fn (Ticket $record) => $record->isFollowedBy(auth()->id()) ? 'Unfollow' : 'Follow')
            ->icon(fn (Ticket $record) => $record->isFollowedBy(auth()->id()) ? 'heroicon-s-bell-slash' : 'heroicon-o-bell')
            ->color('gray')
            ->action(function (Ticket $record): void {
                if ($record->isFollowedBy(auth()->id())) {
                    $record->unfollow(auth()->id());

                    Notification::make()
                        ->title('Unfollowed ticket')
                        ->success()
                        ->send();
                } else {
                    $record->follow(auth()->id());

                    Notification::make()
                        ->title('Now following ticket')
                        ->success()
                        ->send();
                }
            });
    }
}

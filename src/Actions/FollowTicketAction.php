<?php

namespace Escalated\Filament\Actions;

use Escalated\Laravel\Models\Ticket;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

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
            ->label(fn (Ticket $record) => $record->isFollowedBy(auth()->id()) ? __('escalated-filament::filament.actions.follow_ticket.unfollow') : __('escalated-filament::filament.actions.follow_ticket.follow'))
            ->icon(fn (Ticket $record) => $record->isFollowedBy(auth()->id()) ? 'heroicon-s-bell-slash' : 'heroicon-o-bell')
            ->color('gray')
            ->action(function (Ticket $record): void {
                if ($record->isFollowedBy(auth()->id())) {
                    $record->unfollow(auth()->id());

                    Notification::make()
                        ->title(__('escalated-filament::filament.actions.follow_ticket.unfollowed'))
                        ->success()
                        ->send();
                } else {
                    $record->follow(auth()->id());

                    Notification::make()
                        ->title(__('escalated-filament::filament.actions.follow_ticket.now_following'))
                        ->success()
                        ->send();
                }
            });
    }
}

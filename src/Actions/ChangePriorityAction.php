<?php

namespace Escalated\Filament\Actions;

use Escalated\Laravel\Enums\TicketPriority;
use Escalated\Laravel\Models\Ticket;
use Escalated\Laravel\Services\TicketService;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;

class ChangePriorityAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'changePriority';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Change Priority')
            ->icon('heroicon-o-flag')
            ->color('warning')
            ->form([
                Forms\Components\Select::make('priority')
                    ->label('New Priority')
                    ->options(collect(TicketPriority::cases())->mapWithKeys(
                        fn (TicketPriority $p) => [$p->value => $p->label()]
                    ))
                    ->required(),
            ])
            ->action(function (Ticket $record, array $data): void {
                app(TicketService::class)->changePriority(
                    $record,
                    TicketPriority::from($data['priority']),
                    auth()->user()
                );

                Notification::make()
                    ->title('Priority updated successfully')
                    ->success()
                    ->send();
            });
    }
}

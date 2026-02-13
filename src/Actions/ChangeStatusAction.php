<?php

namespace Escalated\Filament\Actions;

use Escalated\Laravel\Enums\TicketStatus;
use Escalated\Laravel\Models\Ticket;
use Escalated\Laravel\Services\TicketService;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;

class ChangeStatusAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'changeStatus';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('escalated-filament::filament.actions.change_status.label'))
            ->icon('heroicon-o-arrow-path')
            ->color('warning')
            ->form([
                Forms\Components\Select::make('status')
                    ->label(__('escalated-filament::filament.actions.change_status.new_status_field'))
                    ->options(collect(TicketStatus::cases())->mapWithKeys(
                        fn (TicketStatus $s) => [$s->value => $s->label()]
                    ))
                    ->required(),
            ])
            ->action(function (Ticket $record, array $data): void {
                app(TicketService::class)->changeStatus(
                    $record,
                    TicketStatus::from($data['status']),
                    auth()->user()
                );

                Notification::make()
                    ->title(__('escalated-filament::filament.actions.change_status.success'))
                    ->success()
                    ->send();
            });
    }
}

<?php

namespace Escalated\Filament\Actions;

use Escalated\Laravel\Escalated;
use Escalated\Laravel\Models\Ticket;
use Escalated\Laravel\Services\AssignmentService;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;

class AssignTicketAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'assignTicket';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Assign')
            ->icon('heroicon-o-user-plus')
            ->color('primary')
            ->form([
                Forms\Components\Select::make('agent_id')
                    ->label('Agent')
                    ->options(fn () => app(Escalated::userModel())::pluck('name', 'id'))
                    ->searchable()
                    ->required(),
            ])
            ->action(function (Ticket $record, array $data): void {
                app(AssignmentService::class)->assign($record, $data['agent_id'], auth()->user());

                Notification::make()
                    ->title('Ticket assigned successfully')
                    ->success()
                    ->send();
            })
            ->visible(fn (Ticket $record) => $record->isOpen());
    }
}

<?php

namespace Escalated\Filament\Resources\TicketResource\Pages;

use Escalated\Filament\Resources\TicketResource;
use Escalated\Laravel\Enums\TicketPriority;
use Escalated\Laravel\Enums\TicketStatus;
use Escalated\Laravel\Models\Ticket;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListTickets extends ListRecords
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Tickets')
                ->icon('heroicon-o-inbox')
                ->badge(Ticket::count()),

            'my_tickets' => Tab::make('My Tickets')
                ->icon('heroicon-o-user')
                ->badge(Ticket::where('assigned_to', auth()->id())->open()->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('assigned_to', auth()->id())),

            'unassigned' => Tab::make('Unassigned')
                ->icon('heroicon-o-user-minus')
                ->badge(Ticket::unassigned()->open()->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->unassigned()->open()),

            'urgent' => Tab::make('Urgent')
                ->icon('heroicon-o-exclamation-triangle')
                ->badge(
                    Ticket::open()
                        ->whereIn('priority', [TicketPriority::Urgent->value, TicketPriority::Critical->value])
                        ->count()
                )
                ->modifyQueryUsing(fn (Builder $query) => $query->open()
                    ->whereIn('priority', [TicketPriority::Urgent->value, TicketPriority::Critical->value])),

            'sla_breaching' => Tab::make('SLA Breaching')
                ->icon('heroicon-o-clock')
                ->badge(Ticket::open()->breachedSla()->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->open()->breachedSla()),
        ];
    }
}

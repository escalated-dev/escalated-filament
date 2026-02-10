<?php

namespace Escalated\Filament\Widgets;

use Escalated\Filament\Resources\TicketResource;
use Escalated\Laravel\Enums\TicketPriority;
use Escalated\Laravel\Enums\TicketStatus;
use Escalated\Laravel\Models\Ticket;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentTicketsWidget extends BaseWidget
{
    protected static ?string $heading = 'Recent Tickets';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Ticket::query()
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label('Ref')
                    ->weight('bold')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('subject')
                    ->limit(40),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (TicketStatus $state): string => match ($state) {
                        TicketStatus::Open, TicketStatus::Reopened => 'info',
                        TicketStatus::InProgress => 'primary',
                        TicketStatus::WaitingOnCustomer, TicketStatus::WaitingOnAgent => 'warning',
                        TicketStatus::Escalated => 'danger',
                        TicketStatus::Resolved => 'success',
                        TicketStatus::Closed => 'gray',
                    })
                    ->formatStateUsing(fn (TicketStatus $state) => $state->label()),

                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn (TicketPriority $state): string => match ($state) {
                        TicketPriority::Low => 'gray',
                        TicketPriority::Medium => 'info',
                        TicketPriority::High => 'warning',
                        TicketPriority::Urgent => 'warning',
                        TicketPriority::Critical => 'danger',
                    })
                    ->formatStateUsing(fn (TicketPriority $state) => $state->label()),

                Tables\Columns\TextColumn::make('assignee.name')
                    ->label('Agent')
                    ->default('Unassigned'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->since(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Ticket $record) => TicketResource::getUrl('view', ['record' => $record])),
            ])
            ->paginated(false);
    }
}

<?php

namespace Escalated\Filament\Widgets;

use Escalated\Filament\Resources\TicketResource;
use Escalated\Laravel\Enums\TicketPriority;
use Escalated\Laravel\Models\Ticket;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class SlaBreachWidget extends BaseWidget
{
    protected static ?string $heading = 'SLA Breach Alerts';

    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Ticket::query()
                    ->open()
                    ->breachedSla()
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label('Ref')
                    ->weight('bold')
                    ->color('danger'),

                Tables\Columns\TextColumn::make('subject')
                    ->limit(40),

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

                Tables\Columns\IconColumn::make('sla_first_response_breached')
                    ->label('Response')
                    ->boolean()
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),

                Tables\Columns\IconColumn::make('sla_resolution_breached')
                    ->label('Resolution')
                    ->boolean()
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),

                Tables\Columns\TextColumn::make('resolution_due_at')
                    ->label('Resolution Due')
                    ->dateTime()
                    ->color('danger'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Ticket $record) => TicketResource::getUrl('view', ['record' => $record])),
            ])
            ->paginated(false)
            ->emptyStateHeading('No SLA Breaches')
            ->emptyStateDescription('All tickets are within SLA targets.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}

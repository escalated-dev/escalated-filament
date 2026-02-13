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
    public function getHeading(): ?string
    {
        return __('escalated-filament::filament.widgets.sla_breach.heading');
    }

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
                    ->label(__('escalated-filament::filament.widgets.sla_breach.column_ref'))
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
                    ->label(__('escalated-filament::filament.widgets.sla_breach.column_agent'))
                    ->default(__('escalated-filament::filament.widgets.sla_breach.unassigned')),

                Tables\Columns\IconColumn::make('sla_first_response_breached')
                    ->label(__('escalated-filament::filament.widgets.sla_breach.column_response'))
                    ->boolean()
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),

                Tables\Columns\IconColumn::make('sla_resolution_breached')
                    ->label(__('escalated-filament::filament.widgets.sla_breach.column_resolution'))
                    ->boolean()
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),

                Tables\Columns\TextColumn::make('resolution_due_at')
                    ->label(__('escalated-filament::filament.widgets.sla_breach.column_resolution_due'))
                    ->dateTime()
                    ->color('danger'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Ticket $record) => TicketResource::getUrl('view', ['record' => $record])),
            ])
            ->paginated(false)
            ->emptyStateHeading(__('escalated-filament::filament.widgets.sla_breach.empty_heading'))
            ->emptyStateDescription(__('escalated-filament::filament.widgets.sla_breach.empty_description'))
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}

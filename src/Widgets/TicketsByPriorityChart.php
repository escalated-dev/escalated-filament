<?php

namespace Escalated\Filament\Widgets;

use Escalated\Laravel\Enums\TicketPriority;
use Escalated\Laravel\Models\Ticket;
use Filament\Widgets\ChartWidget;

class TicketsByPriorityChart extends ChartWidget
{
    public function getHeading(): ?string
    {
        return __('escalated-filament::filament.widgets.tickets_by_priority.heading');
    }

    protected static ?int $sort = 3;

    public function getMaxHeight(): ?string
    {
        return '300px';
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $data = collect(TicketPriority::cases())->map(function (TicketPriority $priority) {
            return [
                'label' => $priority->label(),
                'count' => Ticket::open()->where('priority', $priority->value)->count(),
                'color' => $priority->color(),
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => __('escalated-filament::filament.widgets.tickets_by_priority.open_tickets'),
                    'data' => $data->pluck('count')->all(),
                    'backgroundColor' => $data->pluck('color')->all(),
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $data->pluck('label')->all(),
        ];
    }
}

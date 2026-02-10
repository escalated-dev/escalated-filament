<?php

namespace Escalated\Filament\Widgets;

use Escalated\Laravel\Enums\TicketStatus;
use Escalated\Laravel\Models\Ticket;
use Filament\Widgets\ChartWidget;

class TicketsByStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Tickets by Status';

    protected static ?int $sort = 2;

    protected static ?string $maxHeight = '300px';

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $data = collect(TicketStatus::cases())->map(function (TicketStatus $status) {
            return [
                'label' => $status->label(),
                'count' => Ticket::where('status', $status->value)->count(),
                'color' => $status->color(),
            ];
        })->filter(fn ($item) => $item['count'] > 0);

        return [
            'datasets' => [
                [
                    'data' => $data->pluck('count')->values()->all(),
                    'backgroundColor' => $data->pluck('color')->values()->all(),
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $data->pluck('label')->values()->all(),
        ];
    }
}

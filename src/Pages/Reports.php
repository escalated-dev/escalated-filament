<?php

namespace Escalated\Filament\Pages;

use Escalated\Filament\EscalatedFilamentPlugin;
use Escalated\Laravel\Enums\TicketStatus;
use Escalated\Laravel\Models\Department;
use Escalated\Laravel\Models\SatisfactionRating;
use Escalated\Laravel\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class Reports extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?int $navigationSort = 20;

    protected static ?string $title = 'Reports';

    protected static ?string $slug = 'support-reports';

    protected static string $view = 'escalated-filament::pages.reports';

    public ?string $date_from = null;

    public ?string $date_to = null;

    public static function getNavigationGroup(): ?string
    {
        return app(EscalatedFilamentPlugin::class)->getNavigationGroup();
    }

    public function mount(): void
    {
        $this->date_from = now()->subDays(30)->toDateString();
        $this->date_to = now()->toDateString();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date_from')
                    ->label('From')
                    ->default(now()->subDays(30))
                    ->live(),

                Forms\Components\DatePicker::make('date_to')
                    ->label('To')
                    ->default(now())
                    ->live(),
            ])
            ->columns(2);
    }

    public function getStats(): array
    {
        $from = Carbon::parse($this->date_from)->startOfDay();
        $to = Carbon::parse($this->date_to)->endOfDay();

        $tickets = Ticket::whereBetween('created_at', [$from, $to]);

        $totalTickets = (clone $tickets)->count();
        $resolvedTickets = (clone $tickets)->whereNotNull('resolved_at')->count();

        $avgResponseMinutes = (clone $tickets)
            ->whereNotNull('first_response_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, first_response_at)) as avg_response')
            ->value('avg_response');

        $avgResolutionMinutes = (clone $tickets)
            ->whereNotNull('resolved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, resolved_at)) as avg_resolution')
            ->value('avg_resolution');

        $csatAvg = SatisfactionRating::whereHas('ticket', function ($q) use ($from, $to) {
            $q->whereBetween('created_at', [$from, $to]);
        })->avg('rating');

        return [
            'total_tickets' => $totalTickets,
            'resolved_tickets' => $resolvedTickets,
            'resolution_rate' => $totalTickets > 0 ? round(($resolvedTickets / $totalTickets) * 100, 1) : 0,
            'avg_response_time' => $avgResponseMinutes ? $this->formatMinutes((float) $avgResponseMinutes) : 'N/A',
            'avg_resolution_time' => $avgResolutionMinutes ? $this->formatMinutes((float) $avgResolutionMinutes) : 'N/A',
            'csat_average' => $csatAvg ? round($csatAvg, 1) : 'N/A',
        ];
    }

    public function getTicketsByDepartment(): array
    {
        $from = Carbon::parse($this->date_from)->startOfDay();
        $to = Carbon::parse($this->date_to)->endOfDay();

        return Department::withCount(['tickets' => function ($q) use ($from, $to) {
            $q->whereBetween('created_at', [$from, $to]);
        }])
            ->orderByDesc('tickets_count')
            ->get()
            ->map(fn ($d) => ['name' => $d->name, 'count' => $d->tickets_count])
            ->all();
    }

    public function getTicketsByAgent(): array
    {
        $from = Carbon::parse($this->date_from)->startOfDay();
        $to = Carbon::parse($this->date_to)->endOfDay();

        $userModel = app(\Escalated\Laravel\Escalated::userModel());

        return $userModel::withCount(['tickets' => function ($q) use ($from, $to) {
            $q->whereBetween('created_at', [$from, $to]);
        }])
            ->has('tickets')
            ->orderByDesc('tickets_count')
            ->limit(10)
            ->get()
            ->map(fn ($u) => ['name' => $u->name, 'count' => $u->tickets_count])
            ->all();
    }

    public function getTicketsOverTime(): array
    {
        $from = Carbon::parse($this->date_from)->startOfDay();
        $to = Carbon::parse($this->date_to)->endOfDay();

        return Ticket::whereBetween('created_at', [$from, $to])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn ($r) => ['date' => $r->date, 'count' => $r->count])
            ->all();
    }

    protected function formatMinutes(float $minutes): string
    {
        if ($minutes < 60) {
            return round($minutes).'m';
        }

        $hours = floor($minutes / 60);
        $mins = round($minutes % 60);

        if ($hours < 24) {
            return "{$hours}h {$mins}m";
        }

        $days = floor($hours / 24);
        $hours = $hours % 24;

        return "{$days}d {$hours}h";
    }
}

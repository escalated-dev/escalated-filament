<?php

namespace Escalated\Filament\Widgets;

use Escalated\Laravel\Models\SatisfactionRating;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CsatOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 4;

    public function getHeading(): ?string
    {
        return __('escalated-filament::filament.widgets.csat_overview.heading');
    }

    protected function getStats(): array
    {
        $totalRatings = SatisfactionRating::count();
        $avgRating = SatisfactionRating::avg('rating');

        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = SatisfactionRating::where('rating', $i)->count();
        }

        $positiveRate = $totalRatings > 0
            ? round((($distribution[4] + $distribution[5]) / $totalRatings) * 100, 1)
            : 0;

        return [
            Stat::make(__('escalated-filament::filament.widgets.csat_overview.average_rating'), $avgRating ? number_format($avgRating, 1).'/5' : 'N/A')
                ->description(__('escalated-filament::filament.widgets.csat_overview.overall_average'))
                ->icon('heroicon-o-star')
                ->color($avgRating && $avgRating >= 4 ? 'success' : ($avgRating && $avgRating >= 3 ? 'warning' : 'danger')),

            Stat::make(__('escalated-filament::filament.widgets.csat_overview.total_ratings'), number_format($totalRatings))
                ->description(__('escalated-filament::filament.widgets.csat_overview.all_time'))
                ->icon('heroicon-o-chart-bar')
                ->color('primary'),

            Stat::make(__('escalated-filament::filament.widgets.csat_overview.satisfaction_rate'), $positiveRate.'%')
                ->description(__('escalated-filament::filament.widgets.csat_overview.four_five_star'))
                ->icon('heroicon-o-face-smile')
                ->color($positiveRate >= 80 ? 'success' : ($positiveRate >= 60 ? 'warning' : 'danger')),
        ];
    }
}

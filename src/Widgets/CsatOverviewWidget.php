<?php

namespace Escalated\Filament\Widgets;

use Escalated\Laravel\Models\SatisfactionRating;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CsatOverviewWidget extends StatsOverviewWidget
{
    protected static ?string $heading = 'Customer Satisfaction';

    protected static ?int $sort = 4;

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
            Stat::make('Average Rating', $avgRating ? number_format($avgRating, 1).'/5' : 'N/A')
                ->description('Overall average')
                ->icon('heroicon-o-star')
                ->color($avgRating && $avgRating >= 4 ? 'success' : ($avgRating && $avgRating >= 3 ? 'warning' : 'danger')),

            Stat::make('Total Ratings', number_format($totalRatings))
                ->description('All time')
                ->icon('heroicon-o-chart-bar')
                ->color('primary'),

            Stat::make('Satisfaction Rate', $positiveRate.'%')
                ->description('4 and 5 star ratings')
                ->icon('heroicon-o-face-smile')
                ->color($positiveRate >= 80 ? 'success' : ($positiveRate >= 60 ? 'warning' : 'danger')),
        ];
    }
}

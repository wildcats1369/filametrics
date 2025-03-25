<?php

namespace wildcats1369\Filametrics\Traits;

use Carbon\Carbon;
use Spatie\Analytics\Facades\Analytics;
use Spatie\Analytics\Period;

trait Visitors
{
    use MetricDiff;

    private function visitorsToday($analytics): array
    {
        $analyticsData = $analytics->fetchTotalVisitorsAndPageViews(Period::days(1));

        return [
            'result' => $analyticsData[0]['activeUsers'] ?? 0,
            'previous' => $analyticsData[1]['activeUsers'] ?? 0,
        ];
    }

    private function visitorsYesterday($analytics): array
    {
        $analyticsData = $analytics->fetchTotalVisitorsAndPageViews(Period::create(Carbon::yesterday()->clone()->subDay(), Carbon::yesterday()));

        return [
            'result' => $analyticsData[0]['activeUsers'] ?? 0,
            'previous' => $analyticsData[1]['activeUsers'] ?? 0,
        ];
    }

    private function visitorsLastWeek($analytics): array
    {
        $lastWeek = $this->getLastWeek();
        $currentResults = $this->get('activeUsers', 'date', $lastWeek['current'], $analytics);
        $previousResults = $this->get('activeUsers', 'date', $lastWeek['previous'], $analytics);

        return [
            'previous' => $previousResults->pluck('value')->sum() ?? 0,
            'result' => $currentResults->pluck('value')->sum() ?? 0,
        ];
    }

    private function visitorsLastMonth($analytics): array
    {
        $lastMonth = $this->getLastMonth();
        $currentResults = $this->get('activeUsers', 'year', $lastMonth['current'], $analytics);
        $previousResults = $this->get('activeUsers', 'year', $lastMonth['previous'], $analytics);

        return [
            'previous' => $previousResults->pluck('value')->sum() ?? 0,
            'result' => $currentResults->pluck('value')->sum() ?? 0,
        ];
    }

    private function visitorsLastSevenDays($analytics): array
    {
        $lastSevenDays = $this->getLastSevenDays();
        $currentResults = $this->get('activeUsers', 'year', $lastSevenDays['current'], $analytics);
        $previousResults = $this->get('activeUsers', 'year', $lastSevenDays['previous'], $analytics);

        return [
            'previous' => $previousResults->pluck('value')->sum() ?? 0,
            'result' => $currentResults->pluck('value')->sum() ?? 0,
        ];
    }

    private function visitorsLastThirtyDays($analytics): array
    {
        $lastThirtyDays = $this->getLastThirtyDays();
        $currentResults = $this->get('activeUsers', 'year', $lastThirtyDays['current'], $analytics);
        $previousResults = $this->get('activeUsers', 'year', $lastThirtyDays['previous'], $analytics);

        return [
            'previous' => $previousResults->pluck('value')->sum() ?? 0,
            'result' => $currentResults->pluck('value')->sum() ?? 0,
        ];
    }
}

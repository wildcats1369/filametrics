<?php

namespace wildcats1369\Filametrics\Traits;

use Carbon\Carbon;
use Spatie\Analytics\Period;

trait PageViews
{
    use MetricDiff;

    private function pageViewsToday($analytics): array
    {
        $analyticsData = $analytics->fetchTotalVisitorsAndPageViews(Period::days(1));

        return [
            'result' => $analyticsData->first()['screenPageViews'] ?? 0,
            'previous' => $analyticsData->last()['screenPageViews'] ?? 0,
        ];
    }

    private function pageViewsYesterday($analytics): array
    {
        $analyticsData = $analytics->fetchTotalVisitorsAndPageViews(Period::create(Carbon::yesterday()->clone()->subDay(), Carbon::yesterday()));

        return [
            'result' => $analyticsData->first()['screenPageViews'] ?? 0,
            'previous' => $analyticsData->last()['screenPageViews'] ?? 0,
        ];
    }

    private function pageViewsLastWeek($analytics): array
    {
        $lastWeek = $this->getLastWeek();

        $currentResults = $this->get('screenPageViews', 'year', $lastWeek['current'], $analytics);
        $previousResults = $this->get('screenPageViews', 'year', $lastWeek['previous'], $analytics);

        return [
            'previous' => $previousResults->pluck('value')->sum() ?? 0,
            'result' => $currentResults->pluck('value')->sum() ?? 0,
        ];
    }

    private function pageViewsLastMonth($analytics): array
    {
        $lastMonth = $this->getLastMonth();
        $currentResults = $this->get('screenPageViews', 'year', $lastMonth['current'], $analytics);
        $previousResults = $this->get('screenPageViews', 'year', $lastMonth['previous'], $analytics);

        return [
            'previous' => $previousResults->pluck('value')->sum() ?? 0,
            'result' => $currentResults->pluck('value')->sum() ?? 0,
        ];
    }

    private function pageViewsLastSevenDays($analytics): array
    {
        $lastSevenDays = $this->getLastSevenDays();
        $currentResults = $this->get('screenPageViews', 'year', $lastSevenDays['current'], $analytics);
        $previousResults = $this->get('screenPageViews', 'year', $lastSevenDays['previous'], $analytics);

        return [
            'previous' => $previousResults->pluck('value')->sum() ?? 0,
            'result' => $currentResults->pluck('value')->sum() ?? 0,
        ];
    }

    private function pageViewsLastThirtyDays($analytics): array
    {
        $lastThirtyDays = $this->getLastThirtyDays();
        $currentResults = $this->get('screenPageViews', 'year', $lastThirtyDays['current'], $analytics);
        $previousResults = $this->get('screenPageViews', 'year', $lastThirtyDays['previous'], $analytics);

        return [
            'previous' => $previousResults->pluck('value')->sum() ?? 0,
            'result' => $currentResults->pluck('value')->sum() ?? 0,
        ];
    }
}

<?php

namespace wildcats1369\Filametrics\Traits;

use Carbon\Carbon;
use Spatie\Analytics\Period;

trait SessionsDuration
{
    use MetricDiff;

    private function sessionDurationToday($analytics): array
    {
        $results = $this->get('averageSessionDuration', 'date', Period::days(1), $analytics);

        return match (true) {
            ($results->containsOneItem() && ($results->first()['date'])->isYesterday()) => [
                'previous' => $results->first()['value'],
                'result' => 0,
            ],
            ($results->containsOneItem() && ($results->first()['date'])->isToday()) => [
                'previous' => 0,
                'result' => $results->first()['value'],
            ],
            $results->isEmpty() => [
                'previous' => 0,
                'result' => 0,
            ],
            default => [
                'previous' => $results->last()['value'] ?? 0,
                'result' => $results->first()['value'] ?? 0,
            ]
        };
    }

    private function sessionDurationYesterday($analytics): array
    {
        $results = $this->get('averageSessionDuration', 'date', Period::create(Carbon::yesterday()->clone()->subDay(), Carbon::yesterday()), $analytics);

        return [
            'previous' => $results->last()['value'] ?? 0,
            'result' => $results->first()['value'] ?? 0,
        ];
    }

    private function sessionDurationLastWeek($analytics): array
    {
        $lastWeek = $this->getLastWeek();
        $currentResults = $this->get('averageSessionDuration', 'year', $lastWeek['current'], $analytics);
        $previousResults = $this->get('averageSessionDuration', 'year', $lastWeek['previous'], $analytics);

        return [
            'previous' => $previousResults->pluck('value')[0] ?? 0,
            'result' => $currentResults->pluck('value')[0] ?? 0,
        ];
    }

    private function sessionDurationLastMonth($analytics): array
    {
        $lastMonth = $this->getLastMonth();
        $currentResults = $this->get('averageSessionDuration', 'year', $lastMonth['current'], $analytics);
        $previousResults = $this->get('averageSessionDuration', 'year', $lastMonth['previous'], $analytics);

        return [
            'previous' => $previousResults->pluck('value')[0] ?? 0,
            'result' => $currentResults->pluck('value')[0] ?? 0,
        ];
    }

    private function sessionDurationLastSevenDays($analytics): array
    {
        $lastSevenDays = $this->getLastSevenDays();
        $currentResults = $this->get('averageSessionDuration', 'year', $lastSevenDays['current'], $analytics);
        $previousResults = $this->get('averageSessionDuration', 'year', $lastSevenDays['previous'], $analytics);

        return [
            'previous' => $previousResults->pluck('value')[0] ?? 0,
            'result' => $currentResults->pluck('value')[0] ?? 0,
        ];
    }

    private function sessionDurationLastThirtyDays($analytics): array
    {
        $lastThirtyDays = $this->getLastThirtyDays();
        $currentResults = $this->get('averageSessionDuration', 'year', $lastThirtyDays['current'], $analytics);
        $previousResults = $this->get('averageSessionDuration', 'year', $lastThirtyDays['previous'], $analytics);

        return [
            'previous' => $previousResults->pluck('value')[0] ?? 0,
            'result' => $currentResults->pluck('value')[0] ?? 0,
        ];
    }
}

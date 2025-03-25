<?php

namespace wildcats1369\Filametrics\Traits;

use Carbon\Carbon;
use Spatie\Analytics\Period;

trait Sessions
{
    use MetricDiff;

    private function sessionsToday($analytics): array
    {
        $results = $this->get('sessions', 'date', Period::days(1), $analytics);

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

    private function sessionsYesterday($analytics): array
    {
        $results = $this->get('sessions', 'date', Period::create(Carbon::yesterday()->clone()->subDay(), Carbon::yesterday()), $analytics);

        return [
            'previous' => $results->last()['value'] ?? 0,
            'result' => $results->first()['value'] ?? 0,
        ];
    }

    private function sessionsLastWeek($analytics): array
    {
        $lastWeek = $this->getLastWeek();
        $currentResults = $this->get('sessions', 'year', $lastWeek['current'], $analytics);
        $previousResults = $this->get('sessions', 'year', $lastWeek['previous'], $analytics);

        return [
            'previous' => $previousResults->pluck('value')[0] ?? 0,
            'result' => $currentResults->pluck('value')[0] ?? 0,
        ];
    }

    private function sessionsLastMonth($analytics): array
    {
        $lastMonth = $this->getLastMonth();
        $currentResults = $this->get('sessions', 'year', $lastMonth['current'], $analytics);
        $previousResults = $this->get('sessions', 'year', $lastMonth['previous'], $analytics);

        return [
            'previous' => $previousResults->pluck('value')[0] ?? 0,
            'result' => $currentResults->pluck('value')[0] ?? 0,
        ];
    }

    private function sessionsLastSevenDays($analytics): array
    {
        $lastSevenDays = $this->getLastSevenDays();
        $currentResults = $this->get('sessions', 'year', $lastSevenDays['current'], $analytics);
        $previousResults = $this->get('sessions', 'year', $lastSevenDays['previous'], $analytics);

        return [
            'previous' => $previousResults->pluck('value')[0] ?? 0,
            'result' => $currentResults->pluck('value')[0] ?? 0,
        ];
    }

    private function sessionsLastThirtyDays($analytics): array
    {
        $lastThirtyDays = $this->getLastThirtyDays();
        $currentResults = $this->get('sessions', 'year', $lastThirtyDays['current'], $analytics);
        $previousResults = $this->get('sessions', 'year', $lastThirtyDays['previous'], $analytics);

        return [
            'previous' => $previousResults->pluck('value')[0] ?? 0,
            'result' => $currentResults->pluck('value')[0] ?? 0,
        ];
    }
}

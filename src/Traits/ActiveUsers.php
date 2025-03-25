<?php

namespace wildcats1369\Filametrics\Traits;

use Carbon\Carbon;
use wildcats1369\Filametrics\Helpers\Google\Analytics;
use Spatie\Analytics\Period;

trait ActiveUsers
{
    private function performActiveUsersQuery(string $metric, int $days, $analytics): array
    {
        $analyticsData = $analytics->get(
            Period::days($days),
            [$metric],
            ['date'],
        );

        $results = $analyticsData->mapWithKeys(function ($row) use ($metric) {
            return [
                (new Carbon($row['date']))->format('M j') => $row[$metric],
            ];
        })->sortKeys();
        return ['results' => $results->toArray()];
    }
}

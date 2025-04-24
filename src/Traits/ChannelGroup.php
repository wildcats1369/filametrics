<?php

namespace wildcats1369\Filametrics\Traits;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use wildcats1369\Filametrics\Helpers\Google\Analytics;
use Spatie\Analytics\OrderBy;
use Spatie\Analytics\Period;
use Log;

trait ChannelGroup
{
    use MetricDiff;

    public function channelGroup(string $channelGroup, Period $period, $analytics): Collection
    {
        // Perform the query using the correct metric and dimension
        $metric = 'sessions';
        $dimensions = 'sessionDefaultChannelGroup';
        $results = $analytics->get(
            $period,
            [$metric], // Metric
            [$dimensions], // Dimension
            10, // Limit
            [OrderBy::dimension($dimensions, true)]);

        // Log the results for debugging
        Log::info('Google Analytics API Request:', [
            'period' => $period,
            'metrics' => $metric,
            'dimensions' => $dimensions,
        ]);

        Log::info('Google Analytics API Response:', [
            'data' => $results,
        ]);

        $data = collect($results ?? [])->map(function (array $dateRow) use ($metric, $dimensions) {
            return [
                'group' => $dateRow[$dimensions],
                'value' => $dateRow[$metric],
            ];
        });

        Log::info("Data Collection:", [$data]);


        // Return the results as a collection
        return $data;
    }
}
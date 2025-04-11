<?php

namespace wildcats1369\Filametrics\Helpers\Google;

use Google\Analytics\Data\V1beta\FilterExpression;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;
use Spatie\Analytics\Analytics as SpatieAnalytics;
use Spatie\Analytics\Period;
use Spatie\Analytics\OrderBy;
use Spatie\Analytics\AnalyticsClient;
use Spatie\Analytics\AnalyticsClientFactory;

class Analytics extends SpatieAnalytics
{

    public function fetchChannelGroup(Period $period, int $maxResults = 10, int $offset = 0): Collection
    {
        return $this->get(
            period: $period,
            metrics: ['defaultChannelGroup'],
            dimensions: ['channelGrouping'],
            maxResults: $maxResults,
            orderBy: [
                OrderBy::metric('screenPageViews', true),
            ],
            offset: $offset,
        );
    }

}

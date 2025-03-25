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
    use Macroable;

    /**
     * @var AnalyticsClient
     */
    protected $client;


    public function __construct(
        // protected AnalyticsClient $client,
        protected string $propertyId,
        protected string $service_account_credentials_json,
    ) {
        // $analyticsConfig = [
        //     'service_account_credentials_json' => storage_path('app/private/'.$service_account_credentials_json),
        // ];
        // $authenticatedClient = AnalyticsClientFactory::createAuthenticatedGoogleClient($analyticsConfig);

        // $this->client = AnalyticsClientFactory::createAnalyticsClient($analyticsConfig, $authenticatedClient);

    }

    public function get(
        Period $period,
        array $metrics,
        array $dimensions = [],
        int $maxResults = 10,
        array $orderBy = [],
        int $offset = 0,
        ?FilterExpression $dimensionFilter = null,
        bool $keepEmptyRows = false,
        ?FilterExpression $metricFilter = null,
    ): Collection {
        return $this->client->get(
            $this->propertyId,
            $period,
            $metrics,
            $dimensions,
            $maxResults,
            $orderBy,
            $offset,
            $dimensionFilter,
            $keepEmptyRows,
            $metricFilter,
        );
    }

}

<?php

namespace wildcats1369\Filametrics\Pages;

use wildcats1369\Filametrics\Resources\Filametrics;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use App\Classes\Analytics\CustomGoogleAnalyticsClient;
use Google\Analytics\Data\V1beta\Client\BetaAnalyticsDataClient;
use Spatie\Analytics\Period;
use App\Widgets\Filament\Analytics as WidgetAnalitics;
use Filament\Widgets\WidgetConfiguration;

class ViewGoogleAnalyticsAccount extends ViewRecord
{
    protected static string $resource = Filametrics::class;
    protected $analyticsData, $jsonFilePath, $propertyId;

    public function mount(string|int $record): void
    {
        parent::mount($record);

        // Ensure the record is properly loaded
        $record = $this->getRecord();

        $this->jsonFilePath = storage_path('app/'.$record->service_account_credentials_json);
        $this->propertyId = $record->view_id;

        // Load the analytics data
        // $this->loadAnalyticsData();

        // Print the analytics data
        $this->printAnalyticsData();
    }

    protected function loadAnalyticsData(): void
    {


        $service = new BetaAnalyticsDataClient([
            'credentials' => json_decode(file_get_contents($this->jsonFilePath), true),
        ]);

        $client = new CustomGoogleAnalyticsClient(
            $service,
            app('cache.store'),
            $this->jsonFilePath,
            $this->propertyId,
        );

        $period = Period::days(7);
        $this->analyticsData = $client->getCustomData(
            $period,
            ['activeUsers', 'screenPageViews'],
            ['pageTitle'],
        );
    }

    protected function printAnalyticsData(): void
    {
        echo '<pre>';
        print_r($this->analyticsData);
        echo '</pre>';
    }

    protected function getHeaderWidgets(): array
    {
        \WidgetAnalytics\CustomActiveUsersTwentyEightDayWidget::setAnalyticsData($this->getRecord());

        return [
            \WidgetAnalytics\CustomActiveUsersTwentyEightDayWidget::class,
        ];
    }

}

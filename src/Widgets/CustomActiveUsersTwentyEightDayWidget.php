<?php
namespace WidgetAnalytics;

use BezhanSalleh\FilamentGoogleAnalytics\Widgets\ActiveUsersTwentyEightDayWidget;
use Illuminate\Database\Eloquent\Model;
use App\Class\Analytics\CustomGoogleAnalytics as Analytics;
use Spatie\Analytics\Period;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CustomActiveUsersTwentyEightDayWidget extends ActiveUsersTwentyEightDayWidget
{
    public static $analyticsData = []; // Static property to store the data
    public ?Model $record = null;
    public static $propertyId, $jsonFilePath;

    public function mount(?Model $record = null): void
    {
        $this->record = $record;

        // Log the record for debugging
        \Log::info('Mounted Record:', [self::$propertyId, self::$jsonFilePath]);
    }


    //*******************************
    public static function setAnalyticsData($record): void
    {
        self::$propertyId = $record->view_id;
        self::$jsonFilePath = storage_path('app/'.$record->service_account_credentials_json);
        \Log::info('setAnalyticsData:', [self::$propertyId, self::$jsonFilePath]);
    }

    protected function initializeData()
    {
        // Use the static property to retrieve analytics data
        return [
            'results' => array_column(self::$analyticsData, 'activeUsers', 'pageTitle'),
        ];
    }

    protected function getData(): array
    {

        return [
            'datasets' => [
                [
                    'data' => array_values($this->initializeData()['results']),
                    'borderWidth' => 2,
                    'fill' => 'start',
                    'tension' => 0.5,
                    'pointRadius' => 0,
                    'pointHitRadius' => 0,
                    'backgroundColor' => ['rgba(251, 191, 36, 0.1)'],
                    'borderColor' => ['rgba(245, 158, 11, 1)'],
                ],
            ],
            'labels' => array_values($this->initializeData()['results']),
        ];
    }

    private function performActiveUsersQuery(string $metric, int $days): array
    {

        Analytics::setPropertyId(self::$propertyId);
        $analyticsData = Analytics::get(
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
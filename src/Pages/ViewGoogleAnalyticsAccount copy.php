<?php

namespace wildcats1369\Filametrics\Pages;

use wildcats1369\Filametrics;
use Filament\Resources\Pages\ViewRecord;
use BezhanSalleh\FilamentGoogleAnalytics\Widgets;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class ViewGoogleAnalyticsAccount extends ViewRecord
{
    protected static string $resource = Filametrics::class;

    public function mount(string|int $record): void
    {
        parent::mount($record);

        // Ensure the record is properly loaded
        $record = $this->getRecord();

        // Set the dynamic Google Analytics configuration
        Config::set('analytics.view_id', $record->view_id);
        Config::set('analytics.service_account_credentials_json', storage_path('app/'.$record->service_account_credentials_json));

        // Log the configuration values
        Log::info('Google Analytics Configuration:', [
            'view_id' => Config::get('analytics.view_id'),
            'service_account_credentials_json' => Config::get('analytics.service_account_credentials_json'),
        ]);
    }

    protected function getHeaderWidgets(): array
    {
        // Ensure the record is properly loaded
        $record = $this->getRecord();

        // Set the dynamic Google Analytics configuration again to ensure it's set before rendering widgets
        Config::set('analytics.view_id', $record->view_id);
        Config::set('analytics.service_account_credentials_json', storage_path('app/'.$record->service_account_credentials_json));

        // Log the configuration values again
        Log::info('Google Analytics Configuration (Header Widgets):', [
            'view_id' => Config::get('analytics.view_id'),
            'service_account_credentials_json' => Config::get('analytics.service_account_credentials_json'),
        ]);

        return [
            Widgets\PageViewsWidget::class,
            Widgets\VisitorsWidget::class,
            Widgets\ActiveUsersOneDayWidget::class,
            Widgets\ActiveUsersSevenDayWidget::class,
            Widgets\ActiveUsersTwentyEightDayWidget::class,
            Widgets\SessionsWidget::class,
            Widgets\SessionsDurationWidget::class,
            Widgets\SessionsByCountryWidget::class,
            Widgets\SessionsByDeviceWidget::class,
            Widgets\MostVisitedPagesWidget::class,
            Widgets\TopReferrersListWidget::class,
        ];
    }

    protected function getContent(): string
    {
        return view('filament.resources.google-analytics-account-resource.pages.view-google-analytics-account', [
            'recordId' => $this->record->id,
        ])->render();
    }
}

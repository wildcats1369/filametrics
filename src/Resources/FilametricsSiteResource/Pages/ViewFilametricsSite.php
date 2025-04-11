<?php

namespace wildcats1369\Filametrics\Resources\FilametricsSiteResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use wildcats1369\Filametrics\Resources\FilametricsSiteResource;
use Google\Analytics\Data\V1beta\Client\BetaAnalyticsDataClient;
use wildcats1369\Filametrics\Models\FilametricsSite;
use wildcats1369\Filametrics\Helpers\Google\Widgets;

class ViewFilametricsSite extends ViewRecord
{
    protected static string $resource = FilametricsSiteResource::class;
    public $google_client;

    protected function getFooterWidgets(): array
    {
        return [
                // Widgets\ChannelGroupWidget::class,
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

    public function getDescription(): ?string
    {
        return 'Your description here';
    }

}

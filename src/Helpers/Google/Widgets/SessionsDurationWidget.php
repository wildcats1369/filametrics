<?php

namespace wildcats1369\Filametrics\Helpers\Google\Widgets;

use BezhanSalleh\FilamentGoogleAnalytics\FilamentGoogleAnalytics;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use wildcats1369\Filametrics\Traits;
use BezhanSalleh\FilamentGoogleAnalytics\Traits\CanViewWidget;
use wildcats1369\Filametrics\Models\FilametricsSite;

class SessionsDurationWidget extends ChartWidget
{
    use CanViewWidget;
    use Traits\SessionsDuration;

    protected static ?string $pollingInterval = null;

    protected static string $view = 'filament-google-analytics::widgets.stats-overview';

    protected static ?int $sort = 3;

    public ?string $filter = 'T';

    public ?FilametricsSite $record = null;

    public function getHeading(): string|Htmlable|null
    {
        return __('filament-google-analytics::widgets.sessions_duration');
    }

    protected function getFilters(): array
    {
        return [
            'T' => __('filament-google-analytics::widgets.T'),
            'Y' => __('filament-google-analytics::widgets.Y'),
            'LW' => __('filament-google-analytics::widgets.LW'),
            'LM' => __('filament-google-analytics::widgets.LM'),
            'LSD' => __('filament-google-analytics::widgets.LSD'),
            'LTD' => __('filament-google-analytics::widgets.LTD'),
        ];
    }

    protected function initializeData()
    {
        $analytics = $this->record->getGoogleAnalytics();
        $lookups = [
            'T' => $this->sessionDurationToday($analytics),
            'Y' => $this->sessionDurationYesterday($analytics),
            'LW' => $this->sessionDurationLastWeek($analytics),
            'LM' => $this->sessionDurationLastMonth($analytics),
            'LSD' => $this->sessionDurationLastSevenDays($analytics),
            'LTD' => $this->sessionDurationLastThirtyDays($analytics),
        ];

        $data = Arr::get(
            $lookups,
            $this->filter,
            [
                'result' => 0,
                'previous' => 0,
            ],
        );

        return FilamentGoogleAnalytics::for($data['result'])
            ->previous($data['previous'])
            ->format('%');
    }

    protected function getData(): array
    {
        return [
            'value' => $this->initializeData()->trajectoryValueAsTimeString(),
            'icon' => $this->initializeData()->trajectoryIcon(),
            'color' => $this->initializeData()->trajectoryColor(),
            'description' => $this->initializeData()->trajectoryDescription(),
        ];
    }


    protected function getType(): string
    {
        return 'line';
    }
}
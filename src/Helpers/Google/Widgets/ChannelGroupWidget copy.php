<?php

namespace wildcats1369\Filametrics\Helpers\Google\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Log;

class ChannelGroupWidget extends ChartWidget
{
    protected static ?string $pollingInterval = null;

    protected static string $view = 'filament-google-analytics::widgets.stats-overview';

    protected static ?int $sort = 3;

    public ?string $filter = 'T';

    protected function initializeData()
    {
        // Simulate data for testing
        $data = [
            'results' => [
                '2025-03-01' => 100,
                '2025-03-02' => 150,
                '2025-03-03' => 200,
            ],
        ];
        return $data;
    }

    protected function getType(): string
    {
        return 'line';
    }

    public function getHeading(): string|Htmlable|null
    {
        return __('filament-google-analytics::widgets.page_views');
    }

    protected function getData(): array
    {
        $data = $this->initializeData();
        return [
            'datasets' => [
                [
                    'data' => array_values($data['results']),
                    'borderWidth' => 2,
                    'fill' => 'start',
                    'tension' => 0.5,
                    'pointRadius' => 0,
                    'pointHitRadius' => 0,
                    'backgroundColor' => ['rgba(251, 191, 36, 0.1)'],
                    'borderColor' => ['rgba(245, 158, 11, 1)'],
                ],
            ],
            'labels' => array_keys($data['results']),
        ];
    }
}

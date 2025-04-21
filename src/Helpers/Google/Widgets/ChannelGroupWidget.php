<?php

namespace wildcats1369\Filametrics\Helpers\Google\Widgets;

use wildcats1369\Filametrics\Traits;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Spatie\Analytics\OrderBy;
use Spatie\Analytics\Period;
use wildcats1369\Filametrics\Models\FilametricsSite;
use BezhanSalleh\FilamentGoogleAnalytics\Traits\CanViewWidget;
use Log;

class ChannelGroupWidget extends ChartWidget
{

    use Traits\ChannelGroup;
    use Traits\ChartColors;

    protected static ?string $pollingInterval = null;

    // protected static string $view = 'filament-google-analytics::widgets.stats-overview';

    protected static ?int $sort = 3;

    public ?string $filter = 'T';

    public ?FilametricsSite $record = null;


    protected function initializeData()
    {
        $analytics = $this->record->getGoogleAnalytics();
        return $this->channelGroup('sessions', Period::days(30), $analytics);
    }

    protected function getType(): string
    {
        return 'bar';
    }
    public function getHeading(): string|Htmlable|null
    {
        return "WHERE IS TRAFFIC COMING FROM?";
    }

    protected function getData(): array
    {
        $collection = $this->initializeData()->toArray();

        $labels = [];
        $datasets = [];

        foreach ($collection as $index => $item) {
            $labels[] = $item['group'];
            $datasets[] = [
                'label' => $item['group'].' ('.$item['value'].')',
                'data' => array_fill(0, count($collection), null), // Fill with zeros
                'borderWidth' => 1,
                'fill' => 'start',
                'tension' => 0.5,
                'pointRadius' => 0,
                'pointHitRadius' => 0,
                'backgroundColor' => $this->getColors()[$index]['backgroundColor'],
                'borderColor' => $this->getColors()[$index]['borderColor'],
                'skipNull' => true,
            ];
            $datasets[$index]['data'][$index] = $item['value']; // Set the actual value at the correct index
        }

        return [
            'datasets' => $datasets,
            'labels' => $labels,
        ];
    }








    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y', // Horizontal bar chart
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                ],
            ],
            'plugins' => [
                'datalabels' => [
                    'anchor' => 'end',
                    'align' => 'end',
                    'formatter' => function ($value) {
                        return $value;
                    },
                    'labels' => [
                        'value' => [
                            'color' => '#000',
                        ],
                    ],
                ],
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                    'labels' => [
                        'color' => '#333', // Use 'color' instead of 'fontColor'
                        'font' => [
                            'size' => 12, // Use 'font.size' instead of 'fontSize'
                        ],
                    ],
                ],
            ],
            'maintainAspectRatio' => false,
            'elements' => [
                'bar' => [
                    'barThickness' => 10, // Set the bar thickness
                    'maxBarThickness' => 15, // Set the maximum bar thickness
                    'minBarThinkness' => 10,
                ],
            ],
        ];
    }





}
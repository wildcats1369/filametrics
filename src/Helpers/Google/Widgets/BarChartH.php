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
use Carbon\Carbon;


class BarChartH extends ChartWidget
{
    use Traits\ChartColors;

    protected static ?string $pollingInterval = null;

    // protected static string $view = 'filament-google-analytics::widgets.stats-overview';

    protected static ?int $sort = 3;

    public ?string $filter = 'T';

    public ?FilametricsSite $record = null;

    public $c_heading, $c_description, $period, $metric, $dimensions, $metric_filter, $dimension_filter;

    protected function initializeData()
    {
        $period = Period::create(
            Carbon::parse($this->period['start']),
            Carbon::parse($this->period['end']),
        );

        $analytics = $this->record->getGoogleAnalytics();
        $analyticsData = $analytics->get(
            $period,
            [$this->metric], // Metric
            [$this->dimensions], // Dimension
            10, // Limit
            [OrderBy::dimension($this->dimensions, true)],
            0, //offset
            $this->dimension_filter,
            false,
            $this->metric_filter,
        );

        $metric = $this->metric;
        $dimensions = $this->dimensions;
        $data = collect($analyticsData ?? [])->map(function (array $dateRow) use ($metric, $dimensions) {
            return [
                'group' => $dateRow[$dimensions],
                'value' => $dateRow[$metric],
            ];
        });


        return $data;
    }

    protected function getType(): string
    {
        return 'bar';
    }
    public function getHeading(): string|Htmlable|null
    {
        return $this->c_heading;
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
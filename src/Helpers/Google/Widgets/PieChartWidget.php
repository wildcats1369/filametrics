<?php

namespace wildcats1369\Filametrics\Helpers\Google\Widgets;

use BezhanSalleh\FilamentGoogleAnalytics\Traits\CanViewWidget;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Log;
use Spatie\Analytics\Facades\Analytics;
use Spatie\Analytics\Period;
use wildcats1369\Filametrics\Models\FilametricsSite;
use wildcats1369\Filametrics\Traits;
use Spatie\Analytics\OrderBy;

class PieChartWidget extends ChartWidget
{
    use CanViewWidget;
    use Traits\HasGAFilters;

    protected static string $view = 'filament-google-analytics::widgets.sessions-by-category';

    protected static ?string $pollingInterval = null;

    protected static ?int $sort = 3;

    public ?string $total = null;

    public ?string $filter = 'T';

    public string $category = 'device';

    public ?FilametricsSite $record = null;

    public $c_heading, $c_description, $period, $metric, $dimensions, $metric_filter, $dimension_filter;

    public function mount(): void
    {
        $this->metric = is_array($this->metric) ? $this->metric : [$this->metric];
        $this->dimensions = is_array($this->dimensions) ? $this->dimensions : [$this->dimensions];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    public function getHeading(): string|Htmlable|null
    {
        return __('filament-google-analytics::widgets.sessions');
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
        $period = Period::create(
            Carbon::parse($this->period['start']),
            Carbon::parse($this->period['end']),
        );

        $lookups = [
        ];

        $analyticsData = $this->record->getGoogleAnalytics()->get(
            $period,
            $this->metric, // Metric
            $this->dimensions, // Dimension
            10, // Limit
            [OrderBy::dimension($this->dimensions[0], true)],
            0, //offset
            $this->getGAFilter($this->dimension_filter),
            false,
            $this->getGAFilter($this->metric_filter),
        );

        $results = [];

        foreach ($analyticsData as $row) {
            $results[str($row[$this->dimensions[0]])->studly()->append(' ('.number_format($row[$this->metric[0]]).')')->toString()] = $row[$this->metric[0]];
        }

        $total = 0;
        foreach ($results as $result) {
            $total += $result;
        }

        $this->total = number_format($total);

        return $results;
    }

    protected function getData(): array
    {

        return [
            'labels' => array_keys($this->initializeData()),
            'datasets' => [
                [
                    'label' => 'Device',
                    'data' => array_map('intval', array_values($this->initializeData())),
                    'backgroundColor' => [
                        '#008FFB', '#00E396', '#feb019', '#ff455f', '#775dd0', '#80effe',
                    ],
                    'cutout' => '55%',
                    'hoverOffset' => 5,
                    'borderColor' => 'transparent',
                ],
            ],
        ];
    }

    protected function getOptions(): array|RawJs|null
    {
        return RawJs::make(<<<'JS'
            {
                animation: {
                    duration: 0,
                },
                elements: {
                    point: {
                        radius: 0,
                    },
                    hit: {
                        radius: 0,
                    },

                },
                maintainAspectRatio: false,
                borderRadius: 4,
                scaleBeginAtZero: true,
                radius: '85%',
                plugins: {
                    legend: {
                        display: true,
                        position: 'left',
                        align: 'bottom',
                        labels: {
                            usePointStyle: true,
                            font: {
                                size: 10
                            }
                        }
                    },
                },
                scales: {
                    x: {
                        display: false,
                    },
                    y: {
                        display: false,
                    },
                },
                tooltips: {
                    enabled: false,
                },
            }
        JS);
    }
}

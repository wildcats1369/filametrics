<?php
namespace wildcats1369\Filametrics\Helpers\Google\Widgets;

use wildcats1369\Filametrics\Traits;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Spatie\Analytics\Analytics;
use Spatie\Analytics\Period;
use wildcats1369\Filametrics\Models\FilametricsSite;
use BezhanSalleh\FilamentGoogleAnalytics\Traits\CanViewWidget;
use Log;
use Carbon\Carbon;
use Spatie\Analytics\OrderBy;



class LineChart extends ChartWidget
{
    use Traits\ChartColors;
    use Traits\hasGAFilters;

    protected static ?string $pollingInterval = null;
    protected static ?int $sort = 3;
    public ?string $filter = 'T';
    public ?FilametricsSite $record = null;
    public $c_heading, $c_description, $period, $metric, $dimensions, $metric_filter, $dimension_filter;
    public $dFilter;

    protected function initializeData()
    {
        $this->metric = is_array($this->metric) ? $this->metric : [$this->metric];
        $this->dimensions = is_array($this->dimensions) ? $this->dimensions : [$this->dimensions];

        $analytics = $this->record->getGoogleAnalytics();
        $monthlyData = collect();
        $period = Period::create(
            Carbon::parse($this->period['start']),
            Carbon::parse($this->period['end']),
        );


        for ($i = 0; $i < 12; $i++) {
            $startDate = Carbon::parse($this->period['start'])->subMonths($i)->startOfMonth();
            $endDate = Carbon::parse($this->period['start'])->subMonths($i)->endOfMonth();
            $period = Period::create($startDate, $endDate);

            $data = $analytics->get(
                $period,
                $this->metric, // Metric
                $this->dimensions, // Dimension
                31, // Limit
                [OrderBy::dimension($this->dimensions[0], false)],
                0, //offset
                $this->getGAFilter($this->dimension_filter),
                false,
                $this->getGAFilter($this->metric_filter),
            );

            $monthlyData->push([
                'month' => $startDate->format('F Y'),
                'total' => array_sum(array_column($data->toArray(), $this->metric[0])),
            ]);
        }
        // Prepare data for the chart
        $chartData = $monthlyData->map(function ($monthData) {
            return [
                'group' => $monthData['month'],
                'value' => $monthData['total'],
            ];
        });
        $results = [];
        foreach ($chartData->toArray() as $item) {
            $results[strtotime($item['group'])] = $item;
        }
        ksort($results);
        return $results;
    }

    protected function getType(): string
    {
        return 'line';
    }

    public function getHeading(): string|Htmlable|null
    {
        return $this->c_heading;
    }

    protected function getData(): array
    {
        $dataCollection = $this->initializeData();
        $labels = [];
        $datasets = [];
        $lineData = [];

        foreach ($dataCollection as $item) {
            $labels[] = $item['group'];
            $lineData[] = $item['value'];
        }

        $datasets[] = [
            'label' => 'User Acquisition',
            'data' => $lineData,
            'borderWidth' => 1,
            'fill' => false,
            'tension' => 0.4,
            'pointRadius' => 3,
            'pointHitRadius' => 10,
            'backgroundColor' => '#007bff',
            'borderColor' => '#007bff',
        ];

        return [
            'datasets' => $datasets,
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                ],
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                    'labels' => [
                        'color' => '#333',
                        'font' => [
                            'size' => 12,
                        ],
                    ],
                ],
            ],
            'maintainAspectRatio' => false,
        ];
    }
}

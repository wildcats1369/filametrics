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
use Illuminate\Support\Arr;
use BezhanSalleh\FilamentGoogleAnalytics\FilamentGoogleAnalytics;
use Filament\Support\RawJs;
use Google\Analytics\Data\V1beta\Filter;
use Google\Analytics\Data\V1beta\FilterExpression;
use Google\Analytics\Data\V1beta\Filter\StringFilter;
use Google\Analytics\Data\V1beta\Filter\StringFilter\MatchType;

class TopList extends ChartWidget
{
    use Traits\ChartColors;
    use Traits\SortData;
    use Traits\hasGAFilters;

    protected static ?string $pollingInterval = null;

    protected static string $view = 'filametrics::widgets.google.view.top-list-overview';

    protected static ?int $sort = 3;

    public ?string $filter = 'T';

    public ?FilametricsSite $record = null;

    public $c_heading, $c_description, $period, $metric, $dimensions, $metric_filter, $dimension_filter;
    public $dFilter;

    public function mount(): void
    {
        $this->metric = is_array($this->metric) ? $this->metric : [$this->metric];
        $this->dimensions = is_array($this->dimensions) ? $this->dimensions : [$this->dimensions];
    }

    protected function initializeData()
    {
        $period = Period::create(
            Carbon::parse($this->period['start']),
            Carbon::parse($this->period['end']),
        );

        $current = $this->getPeriodData($period);
        $start = Carbon::parse($this->period['start'])->subMonth()->startOfMonth();
        $end = Carbon::parse($this->period['start'])->subMonth()->endOfMonth();
        $previous_period = Period::create($start, $end);

        $previous_data = $this->getPeriodData($previous_period);

        $previous = array_sum($previous_data->toArray());

        $value = array_sum($current->toArray());
        // dd($value, $previous);
        $data = Arr::get(
            [],
            $this->filter,
            [
                'result' => 0,
                'previous' => 0,
            ],
        );

        $analytics = FilamentGoogleAnalytics::for($value)
            ->previous($previous)
            ->format('%');
        $data = [];

        $result = $this->combineData($current->toArray(), $previous_data->toArray());
        $data['analytics'] = $analytics;
        if ($this->dimensions[0] == 'date') {
            $data['results'] = $this->sortArrayByDate($result);
        } else {
            $data['results'] = $result;
        }

        return $data;
    }


    protected function combineData($current, $previous)
    {
        $result = [];

        // Get the union of keys from both arrays
        $keys = array_unique(array_merge(array_keys($current), array_keys($previous)));

        // Iterate over all countries and populate the result array
        foreach ($keys as $key) {
            $cur_val = $current[$key] ?? 0;
            $prev_val = $previous[$key] ?? 0;
            $data = FilamentGoogleAnalytics::for($cur_val)
                ->previous($prev_val)
                ->format('%');

            if (is_object($data)) {
                $datax = (array) $data;
            } elseif (is_string($data)) {
                $datax = json_decode($data, true);
            }
            $result[$cur_val.'.'.strlen($key)] = $datax;
            $result[$cur_val.'.'.strlen($key)]['field'] = $key;
            $result[$cur_val.'.'.strlen($key)]['icon'] = $data->trajectoryIcon();
            $result[$cur_val.'.'.strlen($key)]['color'] = $data->trajectoryColor();
            $result[$cur_val.'.'.strlen($key)]['description'] = $data->trajectoryDescription();
        }
        ksort($result);
        // dd(array_reverse($result));
        return array_reverse($result);
    }





    protected function getPeriodData($period)
    {


        $analytics = $this->record->getGoogleAnalytics();
        $analyticsData = $analytics->get(
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

        $metric = $this->metric[0];
        $dimension = $this->dimensions[0];
        $results = $analyticsData->mapWithKeys(function ($row) use ($metric, $dimension) {
            if ($dimension == 'date') {
                return [
                    (new Carbon($row['date']))->format('M j') => $row[$metric],
                ];
            } else {
                return [
                    $row[$dimension] => $row[$metric],
                ];
            }

        })->sortKeys();

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

        $collection = $this->initializeData();
        return $collection['results'];

        $labels = [];
        $datasets = [];

        return [
            'datasets' => [
                'data' => array_values($collection['results']),
                'borderWidth' => 2,
                'fill' => 'start',
                'tension' => 0.5,
                'pointRadius' => 0,
                'pointHitRadius' => 0,
                'cutout' => '55%',
                'hoverOffset' => 5,
                // 'borderColor' => 'transparent',
                'backgroundColor' => ['rgba(251, 191, 36, 0.1)'],
                'borderColor' => ['rgba(245, 158, 11, 1)'],
            ],
            'labels' => array_values($collection['results']),
            'value' => $collection['analytics']->value,
            'icon' => $collection['analytics']->trajectoryIcon(),
            'color' => $collection['analytics']->trajectoryColor(),
            'description' => $collection['analytics']->trajectoryDescription(),
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
                        display: false,
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


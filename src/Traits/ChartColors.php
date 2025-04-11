<?php

namespace wildcats1369\Filametrics\Traits;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use wildcats1369\Filametrics\Helpers\Google\Analytics;
use Spatie\Analytics\OrderBy;
use Spatie\Analytics\Period;
use Log;

trait ChartColors
{
    public function getColors(): array
    {
        return [
            [
                'backgroundColor' => 'rgba(219, 68, 55, 0.2)',
                'borderColor' => 'rgba(219, 68, 55, 1)',
            ],
            [
                'backgroundColor' => 'rgba(66, 133, 244, 0.2)',
                'borderColor' => 'rgba(66, 133, 244, 1)',
            ],
            [
                'backgroundColor' => 'rgba(244, 180, 0, 0.2)',
                'borderColor' => 'rgba(244, 180, 0, 1)',
            ],
            [
                'backgroundColor' => 'rgba(15, 157, 88, 0.2)',
                'borderColor' => 'rgba(15, 157, 88, 1)',
            ],
            [
                'backgroundColor' => 'rgba(255, 167, 38, 0.2)',
                'borderColor' => 'rgba(255, 167, 38, 1)',
            ],
            [
                'backgroundColor' => 'rgba(138, 43, 226, 0.2)',
                'borderColor' => 'rgba(138, 43, 226, 1)',
            ],
            [
                'backgroundColor' => 'rgba(0, 150, 136, 0.2)',
                'borderColor' => 'rgba(0, 150, 136, 1)',
            ],
            [
                'backgroundColor' => 'rgba(233, 30, 99, 0.2)',
                'borderColor' => 'rgba(233, 30, 99, 1)',
            ],
            [
                'backgroundColor' => 'rgba(3, 169, 244, 0.2)',
                'borderColor' => 'rgba(3, 169, 244, 1)',
            ],
            [
                'backgroundColor' => 'rgba(174, 234, 0, 0.2)',
                'borderColor' => 'rgba(174, 234, 0, 1)',
            ],
        ];
    }

}
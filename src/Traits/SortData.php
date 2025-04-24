<?php

namespace wildcats1369\Filametrics\Traits;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use wildcats1369\Filametrics\Helpers\Google\Analytics;
use Spatie\Analytics\OrderBy;
use Spatie\Analytics\Period;
use Log;

trait SortData
{
    use MetricDiff;
    function sortArrayByDate($array)
    {
        $dates = array_keys($array);
        usort($dates, function ($a, $b) {
            $dateA = Carbon::createFromFormat('M d', $a);
            $dateB = Carbon::createFromFormat('M d', $b);
            return $dateA->gt($dateB);
        });

        $sortedArray = [];
        foreach ($dates as $date) {
            $sortedArray[$date] = $array[$date];
        }

        return $sortedArray;
    }
}
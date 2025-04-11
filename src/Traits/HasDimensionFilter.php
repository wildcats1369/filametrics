<?php

namespace wildcats1369\Filametrics\Traits;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use wildcats1369\Filametrics\Helpers\Google\Analytics;
use Spatie\Analytics\OrderBy;
use Spatie\Analytics\Period;
use Log;
use Google\Analytics\Data\V1beta\Filter;
use Google\Analytics\Data\V1beta\FilterExpression;
use Google\Analytics\Data\V1beta\Filter\StringFilter;
use Google\Analytics\Data\V1beta\Filter\StringFilter\MatchType;

trait HasDimensionFilter
{
    public function getDimensionFilter($dimension_filter)
    {
        $filter = null;

        if (! empty($dimension_filter)) {
            list($filterDimension, $filterValue) = explode(':', $dimension_filter);
            $filter = new FilterExpression([
                'filter' => new Filter([
                    'field_name' => $filterDimension,
                    'string_filter' => new StringFilter([
                        'match_type' => MatchType::EXACT,
                        'value' => ucfirst($filterValue),
                    ]),
                ]),
            ]);
        }

        return $filter;
    }

}
<?php

namespace App\QueryFilters;

use Illuminate\Database\Eloquent\Builder;

class ExactFilter
{
    public function __invoke(Builder $query, $value, $filterName)
    {
        dd($query, $value, $filterName);
        return $query->where($filterName, $value);
    }
}

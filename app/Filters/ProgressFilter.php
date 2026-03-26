<?php

namespace App\Filters;

use Carbon\Carbon;
use Essa\APIToolKit\Filters\QueryFilters;

class ProgressFilter extends QueryFilters
{
    protected array $allowedFilters = ['status', 'category_id', 'system_id'];

    protected array $columnSearch = [];

    public function team_id($value)
    {
        $this->builder->where('team_id', $value);
    }

     public function raised_from($value)
    {
        $this->builder->whereDate('raised_date', '>=', Carbon::parse($value)->toDateString());
    }

    public function raised_to($value)
    {
        $this->builder->whereDate('raised_date', '<=', Carbon::parse($value)->toDateString());
    } 
}

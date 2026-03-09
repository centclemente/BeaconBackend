<?php

namespace App\Filters;

use Essa\APIToolKit\Filters\QueryFilters;

class TeamFilter extends QueryFilters
{
    protected array $allowedFilters = [];

    protected array $columnSearch = [
        'name',
        'code',      
    ];
}
 
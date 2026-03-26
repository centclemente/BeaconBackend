<?php

namespace App\Models;

use App\Filters\ChargingFilter;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Charging extends Model
{
    use HasFactory, Filterable, SoftDeletes;

    protected $table = 'charging';

    protected string $default_filters = ChargingFilter::class;

    protected $fillable = [
        'code',
        'name',
        'company_id',
        'company_code',
        'company_name',
        'business_unit_id',
        'business_unit_code',
        'business_unit_name',
        'department_id',
        'department_code',
        'department_name',
        'unit_id',
        'unit_code',
        'unit_name',
        'sub_unit_id',
        'sub_unit_code',
        'sub_unit_name',
        'location_id',
        'location_code',
        'location_name',
        
        ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}

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
        'name',
        'code',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}

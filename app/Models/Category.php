<?php

namespace App\Models;

use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Filters\CategoryFilter;
use App\Models\Systems;
use App\Models\Progress;


class Category extends Model
{
    use HasFactory,SoftDeletes,Filterable ;

    protected string $default_filters =CategoryFilter::class;

    protected $table='category';
    protected $fillable = [
        'name',
        ];

  

    public function progress()
    {
        return $this->hasMany(Progress::class);
    }
}

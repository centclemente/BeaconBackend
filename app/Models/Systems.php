<?php

namespace App\Models;

use App\Filters\SystemFilter;
use App\Models\Category;
use App\Models\Progress;
use App\Models\Team;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Systems extends Model
{
    use HasFactory,Filterable,SoftDeletes;

    protected string $default_filters = SystemFilter::class;

    protected $fillable = [
        'name',
        'code',
    ];

    public function team()
    {
     return $this->belongsToMany(
        Team::class,          
        'system_team',        
        'system_id',         
        'team_id'             
    );
    }

    public function progress()
    {
        return $this->hasMany(Progress::class, 'system_id');
    }
}

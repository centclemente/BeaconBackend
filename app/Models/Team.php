<?php

namespace App\Models;

use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Systems; 
use App\Models\User;
use App\Filters\TeamFilter;


class Team extends Model
{
    use HasFactory,Filterable,SoftDeletes;

    protected string $default_filters = TeamFilter::class;
    protected $fillable = [
        'name',
        'code'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
    
    public function systems()
    {
        return $this->hasMany(Systems::class);
    }



}

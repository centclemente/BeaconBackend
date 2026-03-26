<?php

namespace App\Models;

use App\Filters\ProgressFilter;
use App\Models\Category;
use App\Models\Systems;
use App\Models\Team;
use App\Models\User;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Progress extends Model
{
    use HasFactory,SoftDeletes,Filterable;

    protected string $default_filters = ProgressFilter::class;


    protected $fillable = [
        'description',
        'raised_date',
        'target_date',
        'end_date',
        'status',
        'remarks',
        'category_id',
        'system_id',
        'team_id',
        'updated_by',
    ];


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function systems()
    {
        return $this->belongsTo(Systems::class, 'system_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}

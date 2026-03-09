<?php

namespace App\Models;

use App\Models\Category;
use App\Models\Systems;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Team;
use Illuminate\Database\Eloquent\SoftDeletes;

class Progress extends Model
{
    use HasFactory,SoftDeletes;


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
}

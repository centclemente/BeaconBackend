<?php

namespace App\Models;

use App\Filters\RoleFilter;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Role extends Model
{
    use HasFactory, Filterable, softDeletes;

    protected $table = 'role';

    protected string $default_filters = RoleFilter::class;  

    protected $fillable = [
        'name',
        'access_permissions',
    ];
    protected $casts = [
        'access_permissions' => 'array',
    ];
    public function users()
    {
        return $this->hasMany(User::class);
    }

}

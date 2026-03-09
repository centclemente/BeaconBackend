<?php

namespace App\Models;

use App\Filters\UserFilter;
use Laravel\Sanctum\HasApiTokens;
use Essa\APIToolKit\Filters\Filterable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\Role;
use App\Models\Charging;
use App\Models\Team;


class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, Filterable, softDeletes;

    protected string $default_filters = UserFilter::class;


    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'username',
        'password',
        'role_id',
        'charging_id',
        'team_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'role_id' => $this->role_id,
            'team_id' => $this->team_id,
            'charging_id' => $this->charging_id,
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function charging()
    {
        return $this->belongsTo(Charging::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}

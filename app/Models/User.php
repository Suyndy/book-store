<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Laravel\Cashier\Billable;

class User extends Authenticatable implements JWTSubject
{   
    use Billable;
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'password', 'is_active',
        //  'phone', 'address', 
         'is_staff',
         'verify_token',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        // 'id' => 'uuid',
        'email_verified_at' => 'datetime',
        // 'password' => 'hashed',
        'is_active' => 'boolean',
        'is_staff' => 'boolean',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}

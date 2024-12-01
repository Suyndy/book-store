<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;  // Thêm SoftDeletes vào model;

    protected $fillable = [
        'name', 'email', 'password', 'is_active', 'phone', 'address', 'is_staff',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'id' => 'uuid',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'is_staff' => 'boolean',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}

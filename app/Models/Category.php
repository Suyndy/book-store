<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;  // Thêm SoftDeletes vào model;

    protected $fillable = [
        'name', 'description',
    ];

    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
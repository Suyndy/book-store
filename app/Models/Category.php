<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Category extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id', 'name', 'description',
    ];

    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
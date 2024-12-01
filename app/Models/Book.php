<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, SoftDeletes;  // Thêm SoftDeletes vào model;

    protected $fillable = [
        'isbn', 'title', 'price', 'author', 'category_id', 'manufacturer', 'published_at', 'description',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}


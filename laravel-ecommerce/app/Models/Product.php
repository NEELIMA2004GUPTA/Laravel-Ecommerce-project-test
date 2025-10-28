<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'title', 'description', 'price', 'discount', 'sku', 'stock', 'variants', 'images'
    ];

    protected $casts = [
        'images' => 'array',
        'variants' => 'array'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

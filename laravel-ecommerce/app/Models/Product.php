<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{

    protected $fillable = [
        'category_id', 'title','slug', 'description', 'price', 'discount', 'sku', 'stock', 'variants', 'images'
    ];

    protected $casts = [
        'images' => 'array',
        'variants' => 'array'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            $product->slug = Str::slug($product->title);
        });

        static::updating(function ($product) {
            $product->slug = Str::slug($product->title);
        });
    }

    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }

}

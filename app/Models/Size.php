<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    protected $guarded = [];

    public function gender()
    {
        return $this->belongsTo(Gender::class, 'gender_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function product_variants()
    {
        return $this->hasMany(ProductVariant::class, 'size_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }
}

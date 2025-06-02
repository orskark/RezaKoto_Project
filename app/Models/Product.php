<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function gender()
    {
        return $this->belongsTo(Gender::class, 'gender_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class, 'enterprise_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function product_variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantImage extends Model
{
    protected $guarded = [];

    public function product_variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }
}

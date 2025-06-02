<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id');
    }

    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function stock()
    {
        return $this->hasOne(Stock::class, 'product_variant_id');
    }

    public function product_variant_images()
    {
        return $this->hasMany(ProductVariantImage::class, 'product_variant_id');
    }

    public function order_details()
    {
        return $this->hasMany(OrderDetail::class, 'product_variant_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $guarded = [];

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product_variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingStatus extends Model
{
    protected $guarded = [];

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function order_shippings()
    {
        return $this->hasMany(OrderShipping::class, 'shipping_status_id');
    }
}

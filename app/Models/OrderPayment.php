<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
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
}

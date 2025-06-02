<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentStatus extends Model
{
    protected $guarded = [];

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'payment_status_id');
    }
}

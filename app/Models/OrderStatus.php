<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderShipping extends Model
{
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (is_null($model->status_id)) {
                $model->status_id = 1; // ID de status "Activo"
            }
        });
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class, 'enterprise_id');
    }

    public function shipping_status()
    {
        return $this->belongsTo(ShippingStatus::class, 'shipping_status_id');
    }
}

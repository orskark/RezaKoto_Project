<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function order_status()
    {
        return $this->belongsTo(OrderStatus::class, 'order_status_id');
    }

    public function order_details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }

    public function order_shippings()
    {
        return $this->hasMany(OrderShipping::class, 'order_id');
    }

    public function order_payments()
    {
        return $this->hasMany(OrderPayment::class, 'order_id');
    }

    public function payment_status()
    {
        return $this->belongsTo(PaymentStatus::class, 'payment_status_id');
    }
}

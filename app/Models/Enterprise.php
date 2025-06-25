<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enterprise extends Model
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

    public function enterprise_type()
    {
        return $this->belongsTo(EnterpriseType::class, 'enterprise_type_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'enterprise_id');
    }

    public function order_shippings()
    {
        return $this->hasMany(OrderShipping::class, 'enterprise_id');
    }
}

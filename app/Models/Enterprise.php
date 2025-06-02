<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enterprise extends Model
{
    protected $guarded = [];

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

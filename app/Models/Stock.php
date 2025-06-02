<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $guarded = [];

    public function product_variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function stock_movements()
    {
        return $this->hasMany(StockMovement::class, 'stock_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    protected $guarded = [];

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function product_variants()
    {
        return $this->hasMany(ProductVariant::class, 'color_id');
    }
}

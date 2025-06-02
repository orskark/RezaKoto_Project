<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = ['name', 'description'];

    public function sizes()
    {
        return $this->hasMany(Size::class, 'brand_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'brand_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }
}

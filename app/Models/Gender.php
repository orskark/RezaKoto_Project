<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gender extends Model
{
    protected $fillable = ['name'];

    public function sizes()
    {
        return $this->hasMany(Size::class, 'gender_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'gender_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $guarded = [];

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class, 'warehouse_id');
    }
}

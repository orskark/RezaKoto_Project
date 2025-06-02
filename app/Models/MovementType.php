<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovementType extends Model
{
    protected $guarded = [];

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function stock_movements()
    {
        return $this->hasMany(StockMovement::class, 'movement_type_id');
    }
}

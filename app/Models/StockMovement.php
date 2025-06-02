<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $guarded = [];

    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stock_id');
    }

    public function movement_type()
    {
        return $this->belongsTo(MovementType::class, 'movement_type_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }
}

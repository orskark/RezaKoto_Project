<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
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

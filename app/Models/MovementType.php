<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovementType extends Model
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

    public function stock_movements()
    {
        return $this->hasMany(StockMovement::class, 'movement_type_id');
    }
}

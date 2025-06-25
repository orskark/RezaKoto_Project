<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRole extends Model
{
    protected $fillable = ['user_id', 'role_id'];

    public function role(): BelongsTo {
        return $this->belongsTo(Role::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

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
}

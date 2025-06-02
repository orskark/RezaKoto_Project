<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnterpriseType extends Model
{
    protected $guarded = [];

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function enterprises()
    {
        return $this->hasMany(Enterprise::class, 'enterprise_type_id');
    }
}

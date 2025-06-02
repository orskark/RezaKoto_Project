<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    protected $fillable = ['name'];

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'document_type_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enterprise extends Model
{

    protected $fillable = ['name','description','NIT','phone_number','address','email',];
}


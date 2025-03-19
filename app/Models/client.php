<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class client extends Model
{
    protected $table = 'client';
    protected $fillable = [
        'name',
        'email',
        'password',
        "role"
    ];
}

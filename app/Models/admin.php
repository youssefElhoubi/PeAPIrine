<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class admin extends Model
{
    protected $table = 'admin';
    protected $fillable = [
        'name',
        'email',
        'password',
        "role"
    ];
}

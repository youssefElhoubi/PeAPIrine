<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class plants extends Model
{
    protected $table = 'palnts';
    protected $fillable = [
        'name',
        'description',
        'price',
        'slug',
        'category_id',
    ];
}

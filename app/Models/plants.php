<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($plant) {
            $plant->slug = Str::slug($plant->name) . '-' . Str::random(6);
        });
    }
}

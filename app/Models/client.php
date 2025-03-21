<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Stmt\Return_;

class client extends Model
{
    protected $table = 'client';
    protected $fillable = [
        'name',
        'email',
        'password',
        "role"
    ];
    public function orders(){
        return $this->hasMany(orders::class,"client_id");
    }
}

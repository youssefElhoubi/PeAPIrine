<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class orders extends Model
{
    protected $table = 'orders';
    protected $fillable = [
        "plant_id",
        "client_id",
        "totale",
        "qauntity"
    ];
    public function plants(){
        return $this->belongsTo(plants::class,"plant_id");
    }
    public function clients(){
        return $this->belongsTo(client::class,"client_id");
    }
}

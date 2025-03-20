<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\auth;
use App\Http\Controllers\plant;

Route::put("auth/singUp",[auth::class,"signUP"]);
Route::put("auth/singUp",[auth::class,"login"]);
Route::middleware(["JWT_validation","isAdmin"])->group(function(){
    Route::post("plant/add",[plant::class,"addPlant"]);
    Route::post("plant/update/{id}",[plant::class,"updatePlant"]);
});

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\auth;
use App\Http\Controllers\plant;
use App\Http\Controllers\Catigoie;

Route::put("auth/singUp",[auth::class,"signUP"]);
Route::put("auth/singUp",[auth::class,"login"]);
Route::middleware(["JWT_validation","isAdmin"])->group(function(){
    Route::post("plant/add",[plant::class,"addPlant"]);
    Route::patch("plant/update/{id}",[plant::class,"updatePlant"]);
    Route::delete("plant/delete/{id}",[plant::class,"deletePlant"]);
    
    Route::post("category/add", [Catigoie::class, "addCategory"]);
    Route::patch("category/update/{id}", [Catigoie::class, "updateCategory"]);
    Route::delete("category/delete/{id}", [Catigoie::class, "deleteCategory"]);
});

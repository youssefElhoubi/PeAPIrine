<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\auth;
use App\Http\Controllers\plant;
use App\Http\Controllers\Catigoie;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\saleController;

Route::post("auth/singup",[auth::class,"signUP"]);
Route::post("auth/singin",[auth::class,"login"]);
Route::middleware(["JWT_validate","isAdmin"])->group(function(){
    Route::post("plant/add",[plant::class,"addPlant"]);
    Route::patch("plant/update/{id}",[plant::class,"updatePlant"]);
    Route::delete("plant/delete/{id}",[plant::class,"deletePlant"]);

    Route::post("category/add", [Catigoie::class, "addcategorie"]);
    Route::patch("category/update/{id}", [Catigoie::class, "updatecategorie"]);
    Route::delete("category/delete/{id}", [Catigoie::class, "deletecategories"]);

    Route::get("statistec/totalesales",[saleController::class,"totaleTales"]);
    Route::get("statistec/popularPlants",[saleController::class,"popularPlants"]);
    Route::get("statistec/salesByCatigory",[saleController::class,"salesByCatigory"]);
});
Route::middleware(["JWT_validate","isClient"])->group(function(){
    Route::post("order/create", [OrderController::class, "createOrder"]);
    Route::patch("order/cancel/{id}", [OrderController::class, "cancelOrder"]);
    Route::get("order/orders", [OrderController::class, "myOrders"]);
});

Route::middleware(["JWT_validate"])->group(function(){
    Route::get("plant/{slug}",[plant::class,"getPlantBySlug"]);
    Route::patch("order/update/{id}",[OrderController::class,"updateStatus"]);
});

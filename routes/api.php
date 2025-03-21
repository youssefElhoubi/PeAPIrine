<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\auth;
use App\Http\Controllers\plant;
use App\Http\Controllers\Catigoie;
use App\Http\Controllers\OrderController;

Route::put("auth/singUp",[auth::class,"signUP"]);
Route::put("auth/singUp",[auth::class,"login"]);
Route::middleware(["JWT_validation","isAdmin"])->group(function(){
    Route::post("plant/add",[plant::class,"addPlant"]);
    Route::patch("plant/update/{id}",[plant::class,"updatePlant"]);
    Route::delete("plant/delete/{id}",[plant::class,"deletePlant"]);
    Route::get("plant/{slug}",[plant::class,"getPlantBySlug"]);

    Route::post("category/add", [Catigoie::class, "addCategory"]);
    Route::patch("category/update/{id}", [Catigoie::class, "updateCategory"]);
    Route::delete("category/delete/{id}", [Catigoie::class, "deleteCategory"]);
});
Route::middleware(["JWT_validation","isClient"])->group(function(){
    Route::post("order/create", [OrderController::class, "createOrder"]);
    Route::patch("order/cancel/{id}", [OrderController::class, "cancelOrder"]);
    Route::get("order/orders", [OrderController::class, "myOrders"]);
});

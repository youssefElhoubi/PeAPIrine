<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\auth;

Route::put("auth/singUp",[auth::class,"signUP"]);
Route::put("auth/singUp",[auth::class,"login"]);

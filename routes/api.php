<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LogoutController;


Route::post('/login', LoginController::class);
Route::post('/register', RegisterController::class);
Route::middleware('auth:sanctum')->post('/logout', LogoutController::class);


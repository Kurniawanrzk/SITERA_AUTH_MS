<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1/auth')->group(function(){
    Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'register']);
    Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
    Route::post('/cek-token', [App\Http\Controllers\Api\AuthController::class, 'cekToken']);
    Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout'])->middleware('auth:api');
    Route::get('/profile', [App\Http\Controllers\Api\AuthController::class, 'profile'])->middleware('auth:api');
    Route::put('edit/profile', [App\Http\Controllers\Api\AuthController::class, 'editProfile'])->middleware('auth:api');
});
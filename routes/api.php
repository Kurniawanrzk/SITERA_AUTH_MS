<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1/auth')->group(function(){
    Route::post('/register', [App\Http\Controllers\API\AuthController::class, 'register']);
    Route::post('/login', [App\Http\Controllers\API\AuthController::class, 'login']);
    Route::post('/cek-token', [App\Http\Controllers\API\AuthController::class, 'cekToken']);
    Route::post('/logout', [App\Http\Controllers\API\AuthController::class, 'logout'])->middleware('auth:api');
    Route::get('/profile', [App\Http\Controllers\API\AuthController::class, 'profile'])->middleware('auth:api');
    Route::put('edit/profile', [App\Http\Controllers\API\AuthController::class, 'editProfile'])->middleware('auth:api');
    Route::post('/register-non-nasabah', [App\Http\Controllers\API\AuthController::class, 'registerNonNasabah']);
    Route::get("/get-inactive-users", [App\Http\Controllers\API\SuperadminController::class, 'getInactiveUsers'])->middleware('auth:api');
    Route::get("/update-status-user/{userId}/{nomorTelepon}/{status}", [App\Http\Controllers\API\SuperadminController::class, 'updateStatusUser'])->middleware('auth:api');

});
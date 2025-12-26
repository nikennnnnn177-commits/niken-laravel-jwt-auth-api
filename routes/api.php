<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['jwt.auth'])->group(function () {

    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/refresh', [AuthController::class, 'refresh']);
    Route::get('/logout', [AuthController::class, 'logout']);

    Route::get('/users',        [UserController::class, 'getUsers']);
    Route::get('/users/{id}',   [UserController::class, 'getUserById']);
    Route::post('/users',       [UserController::class, 'createUser']);
    Route::put('/users/{id}',   [UserController::class, 'updateUser']);
    Route::delete('/users/{id}',[UserController::class, 'deleteUser']);

});


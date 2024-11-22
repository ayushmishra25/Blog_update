<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\LoginController;

// Route for index channel or file 
Route::get('/index', [PostController::class, 'index']);
Route::post('/index', [PostController::class, 'store']);
Route::get('/index/{id}', [PostController::class, 'show']);
Route::put('/index/{id}', [PostController::class, 'update']);
Route::delete('/index/{id}', [PostController::class, 'destroy']);

Route::get('/index/{id}', [SuperAdminController::class, 'show']);

// Auth Routes (Signup & Login)
Route::post('/signup', [AuthController::class, 'signup']);
Route::post('login', [AuthController::class, 'login']);



Route::middleware('auth:sanctum')->group(function() {
    Route::post('logout', [AuthController::class, 'logout']);
    
 
    Route::apiResource('posts', PostController::class);
});

<?php
namespace App\Http\Controllers; 

use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

// Change superadmin view route to index view
Route::view('index', 'index');  // changed from 'superadmin' to 'index'
Route::view('user', 'user');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/user', function () {
    return view('user');
});

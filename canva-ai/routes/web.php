<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CanvaAuthController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/canva/authorize', [CanvaAuthController::class, 'authorizeCanva']);
Route::get('/canva/callback', [CanvaAuthController::class, 'callback']);
Route::get('/canva/revoke', [CanvaAuthController::class, 'revoke']);
Route::get('/canva/authorized', [CanvaAuthController::class, 'isAuthorized']);
Route::get('/canva/return-nav', [CanvaAuthController::class, 'returnNav']);


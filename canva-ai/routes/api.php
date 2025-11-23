
<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CanvaAuthController;
use App\Http\Controllers\CanvaDesignController;
use App\Http\Controllers\AuthController;




Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/canva/designs/create', [CanvaDesignController::class, 'createDesign']);
Route::post('/canva/designs/export', [CanvaDesignController::class, 'exportDesign']);
Route::post('/canva/designs/download', [CanvaDesignController::class, 'downloadExport']);

Route::get('/canva/authorize', [CanvaAuthController::class, 'authorizeCanva']);
Route::get('/canva/callback', [CanvaAuthController::class, 'callback']);
Route::get('/canva/revoke', [CanvaAuthController::class, 'revoke']);
Route::get('/canva/authorized', [CanvaAuthController::class, 'isAuthorized']);
Route::get('/canva/return-nav', [CanvaAuthController::class, 'returnNav']);
Route::get('/canva/token', [CanvaDesignController::class, 'getToken']);
Route::get('/canva/designs', [CanvaDesignController::class, 'getAllDesigns']);
Route::get('/canva/designs/{id}', [CanvaDesignController::class, 'getDesign']);
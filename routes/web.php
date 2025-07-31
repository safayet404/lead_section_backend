<?php

use App\Http\Controllers\UserController;
use App\Http\Middleware\TokenVerificationMiddleware;
use Illuminate\Support\Facades\Route;

Route::post('/test', function () {
    return response()->json(['status' => 'ok']);
});


Route::post('/registration',[UserController::class, 'UserRegistration']);
Route::post('/login',[UserController::class, 'UserLogin']);
Route::post('/logout',[UserController::class, 'UserLogout']);
Route::post('/reset-password',[UserController::class, 'ResetPassword']);

Route::middleware([TokenVerificationMiddleware::class])->group(function (){
    Route::get("profile", [UserController::class, 'UserProfile']);
});
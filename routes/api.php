<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\PasswordResetController;
use App\Http\Controllers\API\ProfileController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/test', function () {
    return ['user' => User::all()];
});//->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    Route::post('/register', [RegisterController::class, 'register'])->name('api.register');
    Route::post('/login', [LoginController::class, 'login'])->name('api.login');
    Route::delete('/logout', [LoginController::class, 'logout'])->middleware('auth:sanctum')->name('api.logout');
    Route::post('/forgot-password', [PasswordResetController::class, 'forgot'])->name('api.forgot_password');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('api.reset_password');
    Route::post('/reset-password/generate-otp', [PasswordResetController::class, 'generateOtp'])->name('api.generate_otp');
});

Route::prefix('profile')->group(function () {
    
    Route::get('/', [ProfileController::class, 'index'])->name('api.profile_update');
    Route::post('/', [ProfileController::class, 'update'])->name('api.profile_update');
})->middleware('auth:sanctum');
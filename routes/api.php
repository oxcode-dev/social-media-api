<?php

use App\Http\Controllers\Api\FollowerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\PasswordResetController;
use App\Http\Controllers\API\ProfileController;
use App\Models\Follower;

Route::get('/user', function (Request $request) {
    return $request->user();//->with(['followers']);
})->middleware('auth:sanctum');


Route::get('/test', function () {
    return [
        'user' => User::with(['followings', 'followers'])->get(),
        'follower' => Follower::all(),
    ];
});//->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    Route::post('/register', [RegisterController::class, 'register'])->name('api.register');
    Route::post('/login', [LoginController::class, 'login'])->name('api.login');
    Route::delete('/logout', [LoginController::class, 'logout'])->middleware('auth:sanctum')->name('api.logout');
    Route::post('/forgot-password', [PasswordResetController::class, 'forgot'])->name('api.forgot_password');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('api.reset_password');
    Route::post('/reset-password/generate-otp', [PasswordResetController::class, 'generateOtp'])->name('api.generate_otp');
});

Route::middleware(['auth:sanctum'])->prefix('profile')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('api.profile_update');
    Route::post('/', [ProfileController::class, 'update'])->name('api.profile_update');
});

Route::middleware(['auth:sanctum'])->prefix('users')->group(function () {
    Route::post('/{id}/follow', [FollowerController::class, 'store'])->name('api.user_follower');
    Route::delete('/{id}/follow', [FollowerController::class, 'destroy'])->name('api.user_follower');
});
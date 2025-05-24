<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ReservationController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\RoomController;

Route::post('/profile/updatePicture', [UserController::class, 'updatePicture'])->name('profile.updatePicture');
Route::resource('rooms', RoomController::class);

Route::prefix('reservations')->middleware('auth:sanctum')->group(function () {
    Route::apiResource('/', ReservationController::class);
    Route::post('/confirm', [ReservationController::class, 'confirm']);
    Route::post('/{reservation}/edit', [ReservationController::class, 'edit']);
    Route::post('/generateReport', [ReservationController::class, 'generateReport']);
    Route::post('/{reservation}/confirmUpdate', [ReservationController::class, 'confirmUpdate']);
    Route::put('/{reservation}', [ReservationController::class, 'update']);
});


Route::get('/reports', [ReservationController::class, 'showReports'])->name('reports.list');
Route::apiResource('rooms', RoomController::class);

Route::apiResource('users', UserController::class);

Route::controller(AuthController::class)->group(function () {
    Route::get('/profile', 'index');
    Route::post('/login', 'login');
    Route::post('/register', 'register');
    Route::post('/token-check', 'checkToken');
    Route::get('/getuser', 'getCredentialsWithToken')->middleware('auth:sanctum');
});

// ????
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/orderSpecific', [OrderController::class, 'show'])->middleware('auth:sanctum');
Route::post('/orders', [OrderController::class, 'store'])->middleware('auth:sanctum');


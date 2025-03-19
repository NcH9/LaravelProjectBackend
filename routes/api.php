<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\OrderController;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ReservationController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\RoomController;


Route::get('/', function () {
    return view('hello');
});
Route::get('/profile', [AuthController::class, 'index'])->name('profile');
Route::post('/profile/updatePicture', [UserController::class, 'updatePicture'])->name('profile.updatePicture');
Route::resource('rooms', RoomController::class);

Route::prefix('reservations')->middleware('auth:sanctum')->group(function () {
    Route::apiResource('reservations', ReservationController::class);
    Route::post('/reservations/confirm', [ReservationController::class, 'confirm']);
    Route::post('/reservations/{reservation}/edit', [ReservationController::class, 'edit']);
    Route::post('/reservations/generateReport', [ReservationController::class, 'generateReport']);
    Route::post('/reservations/{reservation}/confirmUpdate', [ReservationController::class, 'confirmUpdate']);
    Route::put('/reservations/{reservation}', [ReservationController::class, 'update']);
});


Route::get('/reports', [ReservationController::class, 'showReports'])->name('reports.list');
Route::apiResource('rooms', RoomController::class);

Route::apiResource('users', UserController::class);


Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/getuser', [AuthController::class, 'getCredentialsWithToken'])->middleware('auth:sanctum');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/orderSpecific', [OrderController::class, 'show'])->middleware('auth:sanctum');
Route::post('/orders', [OrderController::class, 'store'])->middleware('auth:sanctum');


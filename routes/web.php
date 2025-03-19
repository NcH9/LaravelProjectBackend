<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;


Route::resource('rooms', RoomController::class);
Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');

Route::get('/', function () {
    return view('hello');
})->name('home');

// Auth routes
Route::get('/profile', [AuthController::class, 'index'])->name('profile');
Route::post('/profile/updatePicture', [UserController::class, 'updatePicture'])->name('profile.updatePicture');

// Reports
Route::get('/reports', [ReservationController::class, 'showReports'])->name('reports.list');

// Reservation routes
Route::resource('reservations', ReservationController::class);
Route::group(['prefix' => 'reservations', 'controller' => ReservationController::class], function () {
    Route::post('/confirm', [ReservationController::class, 'confirm']);

    Route::post('/{reservation}/edit', [ReservationController::class, 'edit']);

    Route::post('/generateReport', [ReservationController::class, 'generateReport']);
    Route::post('/{reservation}/confirmUpdate', [ReservationController::class, 'confirmUpdate']);

    Route::put('/{reservation}', [ReservationController::class, 'update']);
});



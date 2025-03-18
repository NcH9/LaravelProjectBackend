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
Route::post('/reservations/confirm', [ReservationController::class, 'confirm'])
->name('reservations.confirm');

Route::post('/reservations/{reservation}/edit', [ReservationController::class, 'edit'])
->name('reservations.edit');

Route::post('/reservations/generateReport', [ReservationController::class, 'generateReport'])
->name('reservations.generateReport');

Route::post('/reservations/{reservation}/confirmUpdate', [ReservationController::class, 'confirmUpdate'])
->name('reservations.confirmUpdate');

Route::put('/reservations/{reservation}', [ReservationController::class, 'update'])
->name('reservations.update');


<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

// Route::get('/', function () {
//     return view('welcome');
// });
// Route::get('user', [UserController::class, 'index']);
Route::get('/', function () {
    return view('hello');
});
Route::get('/profile', [AuthController::class, 'index'])->name('profile');
Route::post('/profile/updatePicture', [UserController::class, 'updatePicture'])->name('profile.updatePicture');
// Route::get('reservations', [ReservationController::class, 'index'])->middleware('auth');
// Route::get('/rooms', [RoomController::class, 'index'])->name('rooms');
Route::resource('rooms', RoomController::class);
Route::resource('reservations', ReservationController::class);
Route::post('/reservations/confirm', [ReservationController::class, 'confirm'])->name('reservations.confirm');
Route::post('/reservations/{reservation}/edit', [ReservationController::class, 'edit'])->name('reservations.edit');
Route::post('/reservations/generateReport', [ReservationController::class, 'generateReport'])->name('reservations.generateReport');
Route::post('/reservations/{reservation}/confirmUpdate', [ReservationController::class, 'confirmUpdate'])->name('reservations.confirmUpdate');
Route::put('/reservations/{reservation}', [ReservationController::class, 'update'])->name('reservations.update');
Route::get('/reports', [ReservationController::class, 'showReports'])->name('reports.list');
// Route::get('/reports/search', [ReservationController::class, 'searchReports'])->name('reports.search');
// Route::put('/reservations/{reservation}', [ReservationController::class, 'updateUnit'])->name('reservations.updateUnit');
// Route::group([
//     'as' => 'rooms.',
//     'prefix' => 'rooms'
// ], function () {
//     Route::get('/', [RoomController::class, 'index'])->name('index');
//     Route::get('/create', [RoomController::class, 'create'])->name('create');
//     Route::post('/', [RoomController::class, 'store'])->name('store');
//     Route::get('/{room}', [RoomController::class, 'show'])->name('show');
//     Route::get('/{room}/edit', [RoomController::class, 'edit'])->name('edit');
//     Route::put('/{room}', [RoomController::class, 'update'])->name('update');
//     Route::delete('/{room}', [RoomController::class, 'destroy'])->name('destroy');
// });

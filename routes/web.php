<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\PublicController;

// ─── Public ───────────────────────────────────────────────────────────────────
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/rooms', [PublicController::class, 'rooms'])->name('public.rooms');
Route::get('/rooms/{room}', [PublicController::class, 'room'])->name('public.room');
Route::get('/book/{room}', [PublicController::class, 'bookingForm'])->name('public.book');
Route::post('/book/{room}', [PublicController::class, 'bookingStore'])->name('public.book.store');
Route::get('/booking/confirmation/{reservation}', [PublicController::class, 'confirmation'])->name('public.confirmation');

// ─── Logged-in users ──────────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/my-reservations', [PublicController::class, 'myReservations'])->name('my.reservations');
});

// ─── Admin ────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('rooms', RoomController::class);

    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
    Route::post('/reservations/{reservation}/check-in',  [ReservationController::class, 'checkIn']) ->name('reservations.check-in');
    Route::post('/reservations/{reservation}/check-out', [ReservationController::class, 'checkOut'])->name('reservations.check-out');
    Route::post('/reservations/{reservation}/cancel',    [ReservationController::class, 'cancel'])  ->name('reservations.cancel');
    Route::post('/reservations/{reservation}/payment',   [ReservationController::class, 'addPayment'])->name('reservations.payment');
});

require __DIR__.'/auth.php';

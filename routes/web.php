<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/carta', MenuController::class)->name('menu');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');

    Route::get('/registro', [RegisterController::class, 'create'])->name('register');
    Route::post('/registro', [RegisterController::class, 'store'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/reservas',           [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservas/nueva',     [ReservationController::class, 'create'])->name('reservations.create');
    Route::post('/reservas',          [ReservationController::class, 'store'])->name('reservations.store');
    Route::delete('/reservas/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.destroy');
});

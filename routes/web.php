<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MenuItemController as AdminMenuItemController;
use App\Http\Controllers\Admin\ReservationController as AdminReservationController;
use App\Http\Controllers\Admin\TableController as AdminTableController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/render-check', function () {
    $checks = [
        'manifest' => file_exists(public_path('build/manifest.json')),
        'fonts_manifest' => file_exists(public_path('build/fonts-manifest.json')),
        'session_driver' => config('session.driver'),
        'db_connection' => config('database.default'),
        'db_url_set' => (bool) (config('database.connections.pgsql.url') ?: env('DB_URL') ?: env('DATABASE_URL')),
        'hero_video' => config('tikibar.hero_video'),
        'hero_poster_exists' => file_exists(public_path('videos/hero-poster.jpg')),
    ];

    try {
        DB::connection()->getPdo();
        $checks['db'] = 'ok';
        $checks['categories'] = \App\Models\Category::count();
    } catch (Throwable $e) {
        $checks['db'] = $e->getMessage();
    }

    return response()->json($checks);
});

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

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/reservas',                  [AdminReservationController::class, 'index'])->name('reservations.index');
    Route::patch('/reservas/{reservation}',  [AdminReservationController::class, 'update'])->name('reservations.update');

    Route::get('/carta',               [AdminMenuItemController::class, 'index'])->name('menu.index');
    Route::post('/carta',              [AdminMenuItemController::class, 'store'])->name('menu.store');
    Route::patch('/carta/{menuItem}',  [AdminMenuItemController::class, 'update'])->name('menu.update');
    Route::delete('/carta/{menuItem}', [AdminMenuItemController::class, 'destroy'])->name('menu.destroy');

    Route::get('/mesas',            [AdminTableController::class, 'index'])->name('tables.index');
    Route::post('/mesas',           [AdminTableController::class, 'store'])->name('tables.store');
    Route::patch('/mesas/{table}',  [AdminTableController::class, 'update'])->name('tables.update');
    Route::delete('/mesas/{table}', [AdminTableController::class, 'destroy'])->name('tables.destroy');
});

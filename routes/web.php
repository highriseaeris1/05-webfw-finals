<?php

use App\Http\Controllers\Admin\PermintaanController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/kata-sandi', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('/kata-sandi', [PasswordController::class, 'update'])->name('password.update');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Rute Admin: Daftar Permintaan (W7)
|--------------------------------------------------------------------------
| TODO: Tambahkan middleware auth dan pembatasan role admin setelah fitur
| login dan peran pengguna (W2/W3) tersedia.
*/
Route::get('/admin/permintaan', [PermintaanController::class, 'index'])
    ->name('admin.permintaan.index');

// Area khusus Admin; peran lain mendapat 403 dan tamu diarahkan ke login.
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::patch('/users/{user}/status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    });

// Area khusus Pengawas; peran lain mendapat 403 dan tamu diarahkan ke login.
Route::middleware(['auth', 'role:pengawas'])
    ->prefix('pengawas')
    ->name('pengawas.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'pengawas'])->name('dashboard');
    });

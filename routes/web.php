<?php

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

// Area khusus Admin; peran lain mendapat 403 dan tamu diarahkan ke login.
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
    });

// Area khusus Pengawas; peran lain mendapat 403 dan tamu diarahkan ke login.
Route::middleware(['auth', 'role:pengawas'])
    ->prefix('pengawas')
    ->name('pengawas.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'pengawas'])->name('dashboard');
    });

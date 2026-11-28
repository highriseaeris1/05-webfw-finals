<?php

use App\Http\Controllers\Admin\BarangController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\PermintaanController;
use App\Http\Controllers\Admin\PermintaanStatusController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Pengawas\LaporanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

// TODO (Maintainer / Ratu): Tambahkan middleware EnsureUserIsActive pada grup auth untuk memutus sesi akun yang dinonaktifkan saat masih login.
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
| TODO (Maintainer / Atika): Pindahkan rute ini ke grup middleware ['auth', 'role:admin']
| dan sesuaikan tests/Feature/PermintaanIndexTest.php agar memakai actingAs Admin.
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

        // Pengelolaan data master kategori dan barang temuan (W4).
        Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
        Route::get('/kategori/create', [KategoriController::class, 'create'])->name('kategori.create');
        Route::post('/kategori', [KategoriController::class, 'store'])->name('kategori.store');
        Route::get('/kategori/{kategori}/edit', [KategoriController::class, 'edit'])->name('kategori.edit');
        Route::put('/kategori/{kategori}', [KategoriController::class, 'update'])->name('kategori.update');
        Route::delete('/kategori/{kategori}', [KategoriController::class, 'destroy'])->name('kategori.destroy');

        Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
        Route::get('/barang/create', [BarangController::class, 'create'])->name('barang.create');
        Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
        Route::get('/barang/{barang}/edit', [BarangController::class, 'edit'])->name('barang.edit');
        Route::put('/barang/{barang}', [BarangController::class, 'update'])->name('barang.update');
        Route::delete('/barang/{barang}', [BarangController::class, 'destroy'])->name('barang.destroy');

        // Aksi perubahan status permintaan (W6); hanya Admin yang boleh memproses.
        Route::patch('/permintaan/{permintaan}/setujui', [PermintaanStatusController::class, 'setujui'])->name('permintaan.setujui');
        Route::patch('/permintaan/{permintaan}/tolak', [PermintaanStatusController::class, 'tolak'])->name('permintaan.tolak');
        Route::patch('/permintaan/{permintaan}/diambil', [PermintaanStatusController::class, 'tandaiDiambil'])->name('permintaan.diambil');
    });

// Area khusus Pengawas; peran lain mendapat 403 dan tamu diarahkan ke login.
Route::middleware(['auth', 'role:pengawas'])
    ->prefix('pengawas')
    ->name('pengawas.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'pengawas'])->name('dashboard');
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/pdf', [LaporanController::class, 'pdf'])->name('laporan.pdf');
        Route::get('/laporan/excel', [LaporanController::class, 'excel'])->name('laporan.excel');
    });

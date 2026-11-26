<?php

use App\Http\Controllers\Admin\PermintaanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
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

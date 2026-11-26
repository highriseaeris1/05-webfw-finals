<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Arahkan pengguna ke dashboard sesuai peran setelah login atau saat membuka /dashboard.
     */
    public function index(Request $request): RedirectResponse
    {
        return redirect()->route(
            $request->user()->isAdmin() ? 'admin.dashboard' : 'pengawas.dashboard'
        );
    }

    /**
     * Tampilkan ringkasan akun staf untuk Admin.
     */
    public function admin(): View
    {
        return view('dashboard.admin', [
            'totalPengguna' => User::count(),
            'adminAktif' => User::active()->where('role', UserRole::Admin->value)->count(),
            'pengawasAktif' => User::active()->where('role', UserRole::Pengawas->value)->count(),
        ]);
    }

    /**
     * Tampilkan ringkasan pemantauan untuk Pengawas.
     */
    public function pengawas(): View
    {
        return view('dashboard.pengawas');
    }
}

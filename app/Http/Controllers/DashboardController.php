<?php

namespace App\Http\Controllers;

use App\Services\DashboardSummaryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardSummaryService $summaryService,
    ) {}

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
     * Tampilkan ringkasan transaksi, data master, dan akun staf untuk Admin.
     */
    public function admin(): View
    {
        return view('dashboard.admin', $this->summaryService->ringkasanAdmin());
    }

    /**
     * Tampilkan ringkasan pemantauan untuk Pengawas.
     */
    public function pengawas(): View
    {
        return view('dashboard.pengawas', $this->summaryService->ringkasanPengawas());
    }
}

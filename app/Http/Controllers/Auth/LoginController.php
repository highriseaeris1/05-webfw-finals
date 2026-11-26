<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Tampilkan formulir login staf.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Proses login lalu arahkan ke halaman panel.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // Cegah session fixation dengan mengganti ID sesi setelah login berhasil.
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Akhiri sesi pengguna lalu kembalikan ke halaman login.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        // Hapus sesi lama dan buat token CSRF baru agar sesi tidak dapat dipakai ulang.
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Anda telah keluar.');
    }
}

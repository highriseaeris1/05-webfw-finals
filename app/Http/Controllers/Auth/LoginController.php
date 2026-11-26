<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
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
}

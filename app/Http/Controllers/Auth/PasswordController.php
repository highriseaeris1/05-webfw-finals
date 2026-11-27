<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdatePasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PasswordController extends Controller
{
    /**
     * Tampilkan formulir ubah kata sandi.
     */
    public function edit(): View
    {
        return view('auth.password');
    }

    /**
     * Simpan kata sandi baru setelah kata sandi lama diverifikasi.
     */
    public function update(UpdatePasswordRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => Hash::make($request->validated('password')),
        ]);

        return redirect()
            ->route('password.edit')
            ->with('status', 'Kata sandi berhasil diperbarui.');
    }
}

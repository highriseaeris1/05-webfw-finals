<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Pastikan pengguna memiliki salah satu peran yang diizinkan.
     *
     * Tamu diarahkan ke halaman login, sedangkan pengguna dengan peran
     * yang tidak cocok mendapat respons 403 agar URL langsung tetap ditolak.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->guest(route('login'));
        }

        // Bandingkan nilai mentah agar akun dengan role tak dikenal tetap ditolak tanpa error enum.
        if (! in_array($user->getRawOriginal('role'), $roles, true)) {
            abort(403);
        }

        return $next($request);
    }
}

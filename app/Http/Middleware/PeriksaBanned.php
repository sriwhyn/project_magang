<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware untuk memeriksa apakah akun pengguna sedang dinonaktifkan (banned).
 * Jika nonaktif, pengguna diarahkan ke halaman pengajuan banding.
 */
class PeriksaBanned
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->status_akun == 'nonaktif') {
            if ($request->routeIs('banned.*') || $request->routeIs('logout')) {
                return $next($request);
            }
            return redirect()->route('banned.index');
        }

        return $next($request);
    }
}

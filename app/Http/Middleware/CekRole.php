<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CekRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Unauthorized.'], 401);
            }
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk halaman ini.');
        }

        if ($user->role === 'petugas') {
            return redirect()->route('petugas.dashboard')
                ->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk halaman ini.');
        }

        return redirect()->route('home')
            ->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk halaman ini.');
    }
}

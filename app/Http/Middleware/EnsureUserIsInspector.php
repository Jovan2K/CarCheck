<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsInspector
{
    /**
     * Handle an incoming request.
     * Ensure the user is authenticated and has the 'Inspektur Independen' role.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Silakan masuk terlebih dahulu dengan akun Inspektur untuk mengakses fitur inspeksi.',
                ], 401);
            }

            return redirect()->guest(route('login'))
                ->with('error', 'Silakan masuk terlebih dahulu dengan akun Inspektur untuk melakukan inspeksi kendaraan.');
        }

        if (Auth::user()->role !== 'Inspektur Independen') {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Akses ditolak: Akun Anda ('.Auth::user()->role.') tidak memiliki izin untuk melakukan inspeksi. Fitur ini khusus untuk peran Inspektur Independen.',
                ], 403);
            }

            return redirect()->route('dashboard')
                ->with('error', 'Akses ditolak: Akun Anda ('.Auth::user()->role.') tidak memiliki wewenang untuk melakukan inspeksi. Fitur pengisian dan revisi checklist hanya dapat diakses oleh Inspektur Independen.');
        }

        return $next($request);
    }
}

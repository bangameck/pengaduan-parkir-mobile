<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // KITA PAKSA DIA BICARA DI SINI
        // dd('MIDDLEWARE CHECKROLE DIJALANKAN!', 'Role User: ' . $request->user()->role->name, 'Role Dibutuhkan: ', $roles);
        // Cek apakah user sudah login dan memiliki role
        if (! $request->user() || ! $request->user()->role) {
            // Jika tidak, tolak akses (atau redirect ke login)
            return redirect('login');
        }

        // Ambil nama role dari user yang sedang login
        $userRole = $request->user()->role->name;

        // Cek apakah role user ada di dalam daftar role yang diizinkan
        if (in_array($userRole, $roles)) {
            // Jika diizinkan, lanjutkan ke halaman berikutnya
            return $next($request);
        }

        // Jika tidak diizinkan, tampilkan halaman error 403 Forbidden
        abort(403, 'ANDA TIDAK MEMILIKI AKSES KE HALAMAN INI.');
    }
}

<?php
namespace App\Providers;

use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Menjalankan composer setiap kali view 'layouts.navigation' dipanggil
        View::composer('layouts.navigation', function ($view) {
            $pendingReportsCount = 0;
            // Hanya jalankan query jika user sudah login
            if (Auth::check()) {
                // Cek jika user adalah admin atau officer
                if (in_array(Auth::user()->role->name, ['super-admin', 'admin-officer'])) {
                    $pendingReportsCount = Report::where('status', 'pending')->count();
                }
            }
            // Kirim variabel $pendingReportsCount ke view
            $view->with('pendingReportsCount', $pendingReportsCount);
        });
    }
}

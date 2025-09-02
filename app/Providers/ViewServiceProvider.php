<?php
// app/Providers/ViewServiceProvider.php (KODE FINAL YANG BENAR)

namespace App\Providers;

use App\Http\View\Composers\NotificationComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

// <-- Pastikan ini ada

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Daftarkan NotificationComposer untuk view 'layouts.app'
        View::composer('layouts.app', NotificationComposer::class);
    }
}

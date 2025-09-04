<?php
namespace App\Providers;

// NotificationComposer sudah tidak di-import lagi
use App\Models\Report;
use App\Observers\ReportObserver;
// View juga tidak perlu di-import jika hanya untuk composer ini
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Hanya observer yang tersisa di sini
        Report::observe(ReportObserver::class);
        Gate::define('view-super-admin-dashboard', function ($user) {
            return $user->role->name === 'super-admin';
        });
    }
}

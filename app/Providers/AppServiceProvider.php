<?php
namespace App\Providers;

// NotificationComposer sudah tidak di-import lagi
use App\Models\Report;
use App\Models\Setting;
// View juga tidak perlu di-import jika hanya untuk composer ini
use App\Observers\ReportObserver;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
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
        try {
            // Cek dulu apakah tabel settings sudah ada, untuk menghindari error saat migrate pertama kali
            if (Schema::hasTable('settings')) {
                // Ambil semua setting dari cache. Jika tidak ada, ambil dari DB lalu simpan di cache selamanya.
                $settings = Cache::rememberForever('settings', function () {
                    return Setting::all()->pluck('value', 'key')->toArray();
                });

                // Suntikkan setiap setting ke dalam config global Laravel
                foreach ($settings as $key => $value) {
                    config([$key => $value]);
                }
            }
        } catch (\Exception $e) {
            // Jika terjadi error (misalnya koneksi DB gagal), abaikan agar aplikasi tidak crash.
            // Anda bisa menambahkan log di sini jika perlu.
            report($e);
        }
        // Hanya observer yang tersisa di sini
        Report::observe(ReportObserver::class);
        Gate::define('view-super-admin-dashboard', function ($user) {
            return $user->role->name === 'super-admin';
        });
    }
}

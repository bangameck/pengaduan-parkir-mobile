<?php
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider; // Pastikan ini di-import
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // TAMBAHKAN GATE DI SINI

        /**
         * Gate untuk menentukan siapa yang bisa mengelola tim.
         * Hanya 'super-admin' dan 'leader' yang bisa.
         * Asumsi: Model User Anda punya kolom/properti 'role'.
         */
        Gate::define('manage-teams', function ($user) {
            return in_array($user->role, ['super-admin', 'leader']);
        });

        // Anda bisa menambahkan Gate lain di sini nanti
        // Gate::define('view-reports', function ($user) { ... });

    }
}

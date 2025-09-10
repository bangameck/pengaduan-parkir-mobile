<?php
namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
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
        Gate::define('manage-teams', function (User $user) {
            // ==========================================================
            // == PERBAIKAN DI SINI: Gunakan $user->role->name ==
            // ==========================================================
            return in_array($user->role->name, ['leader']);
        });

        Gate::define('delete-reports', function (User $user) {
            return $user->role->name === 'super-admin';
        });

        Gate::define('view-internal-dashboard', function (User $user) {
            return in_array($user->role->name, ['super-admin', 'admin-officer', 'field-officer', 'leader']);
        });

        Gate::define('view-super-admin-menu', function (User $user) {
            return $user->role->name === 'super-admin';
        });

        Gate::define('view-admin-officer-menu', function (User $user) {
            return in_array($user->role->name, ['admin-officer']);
        });

        Gate::define('view-field-officer-menu', function (User $user) {
            return in_array($user->role->name, ['field-officer']);
        });

        Gate::define('view-leader-menu', function (User $user) {
            return in_array($user->role->name, ['leader']);
        });
    }
}

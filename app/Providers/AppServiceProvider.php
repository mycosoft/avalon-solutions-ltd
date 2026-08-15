<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::define('superadmin', function ($user) {
            return $user->role_type === 'superadmin';
        });

        Gate::define('admin', function ($user) {
            return in_array($user->role_type, ['superadmin', 'admin']);
        });

        Gate::define('accountant', function ($user) {
            return in_array($user->role_type, ['superadmin', 'admin', 'accountant']);
        });

        Gate::define('manage users', function ($user) {
            return in_array($user->role_type, ['superadmin', 'admin']);
        });

        Gate::before(function ($user, $ability) {
            if ($user->role_type === 'superadmin') {
                return true;
            }
        });
    }
}

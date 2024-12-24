<?php

namespace App\Providers;

use App\Models\Reservation;
use App\Models\User;
use Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::define('show-and-redact-reservation', function(User $user, Reservation $reservation) {
            if ($user->hasRole('admin') || $user->hasRole('manager')) {
                return true;
            }

            return $user->id === $reservation->user_id;
        });
        Gate::define('is-manager-or-admin', function(User $user) {
            return $user->hasRole('admin') || $user->hasRole('manager');
        });
    }
}

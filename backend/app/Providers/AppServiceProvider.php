<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Gate барои суперадмин — фақат вазорат дастрасии пурра дорад
        Gate::define('superadmin', function ($user) {
            return $user->role === 'superadmin';
        });
    }
}

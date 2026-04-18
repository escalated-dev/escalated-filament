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
        Gate::define('escalated-admin', fn ($user) => (bool) ($user->is_admin ?? false));
        Gate::define('escalated-agent', fn ($user) => (bool) (($user->is_agent ?? false) || ($user->is_admin ?? false)));
    }
}

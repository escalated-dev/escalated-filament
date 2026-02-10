<?php

namespace Escalated\Filament;

use Illuminate\Support\ServiceProvider;

class EscalatedFilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(EscalatedFilamentPlugin::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'escalated-filament');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/escalated-filament'),
            ], 'escalated-filament-views');
        }
    }
}

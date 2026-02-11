<?php

namespace Escalated\Filament\Tests;

use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Escalated\Filament\EscalatedFilamentPlugin;
use Escalated\Filament\EscalatedFilamentServiceProvider;
use Escalated\Laravel\EscalatedServiceProvider;
use Filament\Actions\ActionsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider;

abstract class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Escalated\\Laravel\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );
    }

    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            BladeIconsServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            BladeCaptureDirectiveServiceProvider::class,
            SupportServiceProvider::class,
            FilamentServiceProvider::class,
            FormsServiceProvider::class,
            TablesServiceProvider::class,
            ActionsServiceProvider::class,
            InfolistsServiceProvider::class,
            NotificationsServiceProvider::class,
            WidgetsServiceProvider::class,
            EscalatedServiceProvider::class,
            EscalatedFilamentServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        // SQLite in-memory database
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Escalated config
        $app['config']->set('escalated.table_prefix', 'escalated_');
        $app['config']->set('escalated.user_model', User::class);
        $app['config']->set('escalated.routes.enabled', false);
        $app['config']->set('escalated.inbound_email.enabled', false);
        $app['config']->set('escalated.scheduling.auto_register', false);

        // Filament panel configuration
        $app['config']->set('app.key', 'base64:' . base64_encode(random_bytes(32)));
    }

    protected function defineDatabaseMigrations(): void
    {
        // Create users table for test User model
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        // Load escalated-laravel package migrations
        $this->loadMigrationsFrom(
            dirname(__DIR__) . '/../escalated-laravel/database/migrations'
        );
    }

    /**
     * Register the Filament panel with the Escalated plugin for testing.
     */
    protected function getFilamentPanel(): \Filament\Panel
    {
        return \Filament\Facades\Filament::getDefaultPanel();
    }

    protected function resolveApplicationConfiguration($app): void
    {
        parent::resolveApplicationConfiguration($app);

        // Register a default Filament panel with the Escalated plugin
        $app->booted(function () use ($app) {
            /** @var \Filament\FilamentManager $filament */
            $filament = $app->make(\Filament\FilamentManager::class);

            $panel = \Filament\Panel::make()
                ->default()
                ->id('admin')
                ->path('admin')
                ->login()
                ->plugin(EscalatedFilamentPlugin::make());

            $filament->registerPanel($panel);
        });
    }

    /**
     * Create an authenticated admin user and log in.
     */
    protected function authenticateUser(): User
    {
        $user = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);

        $this->actingAs($user);

        return $user;
    }
}

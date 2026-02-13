<?php

namespace Escalated\Filament\Tests;

use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Escalated\Filament\EscalatedFilamentPlugin;
use Escalated\Filament\EscalatedFilamentServiceProvider;
use Escalated\Laravel\EscalatedServiceProvider;
use Filament\Actions\ActionsServiceProvider;
use Filament\Facades\Filament;
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

abstract class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Escalated\\Laravel\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    protected function getPackageProviders($app): array
    {
        $providers = [
            LivewireServiceProvider::class,
            BladeIconsServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
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

        // Filament v3 dependency — removed in v4/v5
        if (class_exists(\RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider::class)) {
            $providers[] = \RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider::class;
        }

        // Filament v4+ dependency
        if (class_exists(\Filament\Schemas\SchemasServiceProvider::class)) {
            $providers[] = \Filament\Schemas\SchemasServiceProvider::class;
        }

        return $providers;
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

        // Define escalated gates — test user is always agent + admin
        \Illuminate\Support\Facades\Gate::define('escalated-agent', fn ($user) => true);
        \Illuminate\Support\Facades\Gate::define('escalated-admin', fn ($user) => true);
    }

    protected function defineDatabaseMigrations(): void
    {
        // Create users table for test User model
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        // Load escalated-laravel package migrations from vendor
        $migrationPath = dirname(__DIR__) . '/vendor/escalated-dev/escalated-laravel/database/migrations';
        if (! is_dir($migrationPath)) {
            // Fallback for local development with sibling checkout
            $migrationPath = dirname(__DIR__) . '/../escalated-laravel/database/migrations';
        }
        $this->loadMigrationsFrom($migrationPath);
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

        // Register a default Filament panel with the Escalated plugin.
        // Must happen during boot (not in a booted callback) so routes are
        // available when Livewire tests mount components.
        $app->afterResolving(\Filament\FilamentManager::class, function (\Filament\FilamentManager $filament) {
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

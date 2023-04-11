<?php

namespace Vlinde\Bugster;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Fields\Field;
use Vlinde\Bugster\Console\Commands\CheckQueuesStatus;
use Vlinde\Bugster\Console\Commands\CountStatusCodes;
use Vlinde\Bugster\Console\Commands\DeleteOldBugs;
use Vlinde\Bugster\Console\Commands\MoveBugsToSQL;
use Vlinde\Bugster\Console\Commands\NotifyStatistics;
use Vlinde\Bugster\Facades\Bugster;
use Vlinde\Bugster\Http\Middleware\Authorize;

class LaravelBugsterServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'bugster');

        $this->app->booted(function () {
            $this->routes();
        });

        $this->publishes([
            __DIR__.'/../config/bugster.php' => config_path('bugster.php'),
        ], 'bugster-config');

        $this->publishes([
            __DIR__.'/Database/Migrations/create_laravel_bugster_bugs_table.php' => database_path('migrations/'.date('Y_m_d_His').'_create_laravel_bugster_bugs_table.php'),
            __DIR__.'/Database/Migrations/create_laravel_bugster_notifications_table.php' => database_path('migrations/'.date('Y_m_d_His', time() + 1).'_create_laravel_bugster_notifications_table.php'),
            __DIR__.'/Database/Migrations/update_fields_to_laravel_bugster_bugs_table.php' => database_path('migrations/'.date('Y_m_d_His', time() + 2).'_update_fields_to_laravel_bugster_bugs_table.php'),
            __DIR__.'/Database/Migrations/remove_laravel_bugster_stats_table.php' => database_path('migrations/'.date('Y_m_d_His', time() + 3).'_remove_laravel_bugster_stats_table.php'),
            __DIR__.'/Database/Migrations/create_laravel_bugster_status_codes_table.php' => database_path('migrations/'.date('Y_m_d_His', time() + 4).'_create_laravel_bugster_status_codes_table.php'),
        ], 'bugster-migrations');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }

        Field::macro('chart-link', function ($value) {
            return $this->displayUsing(
                view('nova::components.my-link', ['value' => $value])
            );
        });
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware(['nova', Authorize::class])
            ->prefix('nova-vendor/vlinde/bugster')
            ->group(__DIR__.'/../routes/api.php');
    }

    /**
     * Register any package services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/bugster.php', 'bugster');

        $this->registerCommands();

        // Register the service the package provides.
        $this->app->singleton('bugster', function ($app) {
            return new Bugster;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['bugster'];
    }

    protected function registerCommands(): void
    {
        $this->commands([
            DeleteOldBugs::class,
            MoveBugsToSQL::class,
            NotifyStatistics::class,
            CheckQueuesStatus::class,
            CountStatusCodes::class,
        ]);
    }

    /**
     * Console-specific booting.
     */
    protected function bootForConsole(): void
    {
        $this->publishes([
            __DIR__.'/../config/bugster.php' => config_path('bugster.php'),
        ], 'bugster-config');
    }
}

<?php

namespace Vlinde\Bugster;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use Vlinde\Bugster\Console\Commands\CheckQueuesStatus;
use Vlinde\Bugster\Console\Commands\CountStatusCodes;
use Vlinde\Bugster\Console\Commands\DeleteOldBugs;
use Vlinde\Bugster\Console\Commands\MoveBugsToSQL;
use Vlinde\Bugster\Console\Commands\NotifyStatistics;
use Vlinde\Bugster\Http\Middleware\Authorize;
use Vlinde\Bugster\Nova\AdvancedBugsterNotify;
use Vlinde\Bugster\Nova\LaravelBugsterWebhook;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot(): void
    {
        $this->app->booted(function () {
            $this->routes();
        });

        Nova::serving(function (ServingNova $event) {
            Nova::resources([
                AdvancedBugsterNotify::class,
                LaravelBugsterWebhook::class,
            ]);
        });

        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/bugster.php', 'bugster');

        if ($this->app->runningInConsole()) {
            $this->registerCommands();
        }
    }

    /**
     * Register the tool's routes.
     */
    protected function routes(): void
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Nova::router(['nova', 'nova.auth', Authorize::class], 'laravel-bugster')
            ->group(__DIR__.'/../routes/inertia.php');

        Route::middleware(['nova', 'nova.auth', Authorize::class])
            ->prefix('nova-vendor/laravel-bugster')
            ->group(__DIR__.'/../routes/api.php');
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

        $this->publishes([
            __DIR__.'/Database/Migrations/create_laravel_bugster_bugs_table.php' => database_path('migrations/'.date('Y_m_d_His').'_create_laravel_bugster_bugs_table.php'),
            __DIR__.'/Database/Migrations/create_laravel_bugster_notifications_table.php' => database_path('migrations/'.date('Y_m_d_His', time() + 1).'_create_laravel_bugster_notifications_table.php'),
            __DIR__.'/Database/Migrations/update_fields_to_laravel_bugster_bugs_table.php' => database_path('migrations/'.date('Y_m_d_His', time() + 2).'_update_fields_to_laravel_bugster_bugs_table.php'),
            __DIR__.'/Database/Migrations/remove_laravel_bugster_stats_table.php' => database_path('migrations/'.date('Y_m_d_His', time() + 3).'_remove_laravel_bugster_stats_table.php'),
            __DIR__.'/Database/Migrations/create_laravel_bugster_status_codes_table.php' => database_path('migrations/'.date('Y_m_d_His', time() + 4).'_create_laravel_bugster_status_codes_table.php'),
            __DIR__.'/Database/Migrations/update_fields_to_laravel_bugster_notifications_table.php' => database_path('migrations/'.date('Y_m_d_His', time() + 5).'_update_fields_to_laravel_bugster_notifications_table.php'),
            __DIR__.'/Database/Migrations/create_laravel_bugster_webhooks_table.php' => database_path('migrations/'.date('Y_m_d_His', time() + 6).'_create_laravel_bugster_webhooks_table.php'),
            __DIR__.'/Database/Migrations/add_type_to_laravel_bugster_webhooks_table.php' => database_path('migrations/'.date('Y_m_d_His', time() + 7).'_add_type_to_laravel_bugster_webhooks_table.php'),
        ], 'bugster-migrations');
    }
}

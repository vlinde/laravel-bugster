<?php

namespace Vlinde\Bugster;

use Illuminate\Support\ServiceProvider;
use Vlinde\Bugster\Console\Commands\DeleteOldBugs;
use Vlinde\Bugster\Console\Commands\GenerateStats;
use Vlinde\Bugster\Console\Commands\MoveBugsToSQL;
use Vlinde\Bugster\Console\Commands\ParseLogs;
use Vlinde\Bugster\Console\Commands\UpdateBugs;

class LaravelBugsterServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'vlinde');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'vlinde');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        $this->loadViewsFrom(
            __DIR__ . '/../resources/views', 'laravel-bugster'
        );

        $this->publishes([
            __DIR__.'/../config/bugster.php' => config_path('bugster.php'),
        ], 'bugster.config');

        $this->registerCommands();

        $this->publishes([
            __DIR__ . '/Database/Migrations/create_laravel_bugster_bugs_table.php' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_laravel_bugster_bugs_table.php'),
            __DIR__ . '/Database/Migrations/create_laravel_bugster_stats_table.php' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_laravel_bugster_stats_table.php'),
            __DIR__ . '/Database/Migrations/create_laravel_bugster_links_table.php' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_laravel_bugster_links_table.php'),
            __DIR__ . '/Database/Migrations/create_bugster_bug_bugster_link_table.php' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_bugster_bug_bugster_link_table.php'),
            __DIR__ . '/Database/Migrations/create_bugster_link_bugster_stat_table.php' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_bugster_link_bugster_stat_table.php')
            ], 'migrations');


        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
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

    protected function registerCommands(): void
    {
        $this->commands([
            DeleteOldBugs::class,
            GenerateStats::class,
            MoveBugsToSQL::class,
            ParseLogs::class,
            UpdateBugs::class,
        ]);
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

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/bugster.php' => config_path('bugster.php'),
        ], 'bugster.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/vlinde'),
        ], 'laravel-bugster.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/vlinde'),
        ], 'laravel-bugster.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/vlinde'),
        ], 'laravel-bugster.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}

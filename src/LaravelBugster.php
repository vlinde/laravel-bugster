<?php

namespace Vlinde\Bugster;

use Illuminate\Http\Request;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;
use Vlinde\Bugster\Nova\AdvancedBugsterNotify;
use Vlinde\Bugster\Nova\LaravelBugsterWebhook;

class LaravelBugster extends Tool
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        Nova::mix('laravel-bugster', __DIR__.'/../dist/mix-manifest.json');
    }

    /**
     * Build the menu that renders the navigation links for the tool.
     */
    public function menu(Request $request): MenuSection
    {
        return MenuSection::make('Bugster', [
            MenuItem::link('Log Files', '/laravel-bugster/log-files'),
            MenuItem::link('Webhooks', '/resources/'.LaravelBugsterWebhook::uriKey()),
            MenuItem::link('Alerts', '/resources/'.AdvancedBugsterNotify::uriKey()),
            MenuItem::link('Status Codes', '/laravel-bugster/status-codes-chart'),
        ])
            ->icon('bug-ant')->collapsable();
    }
}

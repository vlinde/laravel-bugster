<?php

namespace Vlinde\Bugster;

use Laravel\Nova\Nova;
use Laravel\Nova\Tool;
use Vlinde\Bugster\Nova\AdvancedBugsterDB;
use Vlinde\Bugster\Nova\AdvancedBugsterNotify;
use Vlinde\Bugster\Nova\AdvancedBugsterStat;

class LaravelBugster extends Tool
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        Nova::script("laravel-bugster", __DIR__ . "/../dist/js/tool.js");

        Nova::resources([
            AdvancedBugsterDB::class,
            AdvancedBugsterStat::class,
            AdvancedBugsterNotify::class,
        ]);
    }

    /**
     * Build the view that renders the navigation links for the tool.
     *
     * @return \Illuminate\View\View
     */
    public function renderNavigation()
    {
        return view('laravel-bugster::navigation');
    }
}

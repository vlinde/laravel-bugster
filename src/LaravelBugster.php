<?php

namespace Vlinde\Bugster;

use Laravel\Nova\Nova;
use Laravel\Nova\Tool;
use Vlinde\Bugster\Nova\AdvancedBugsterDB;
use Vlinde\Bugster\Nova\AdvancedBugsterLink;
use Vlinde\Bugster\Nova\AdvancedBugsterStat;

class Bugster extends Tool
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
//        dd("test");

        Nova::script("laravel-bugster", __DIR__."/../dist/js/tool.js");

        Nova::resources([
            AdvancedBugsterDB::class,
            AdvancedBugsterLink::class,
            AdvancedBugsterStat::class,
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

<?php

namespace Vlinde\Bugster\Facades;

use Illuminate\Support\Facades\Facade;

class Bugster extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'bugster';
    }
}

<?php

namespace Vlinde\Bugster\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Resource;
use Vlinde\Bugster\Models\LaravelBugsterStatusCode as LaravelBugsterStatusCodeModel;

class LaravelBugsterStatusCode extends Resource
{
    public static $displayInNavigation = false;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = LaravelBugsterStatusCodeModel::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'display_name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'display_name', 'code', 'date',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()
                ->sortable(),

            Text::make('Display Name')
                ->sortable(),

            Number::make('Code')
                ->sortable(),

            Number::make('Count')
                ->sortable(),

            Date::make('Date')
                ->sortable(),
        ];
    }

    /**
     * Determine if the current user can update the given resource.
     *
     * @return bool
     */
    public function authorizedToUpdate(Request $request)
    {
        return false;
    }

    /**
     * Determine if the current user can create the given resource.
     *
     * @return bool
     */
    public static function authorizedToCreate(Request $request)
    {
        return false;
    }
}

<?php

namespace Vlinde\Bugster\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Resource;


class AdvancedBugsterDB extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Vlinde\Bugster\Models\AdvancedBugsterDB::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'status_code';

    public static $displayInNavigation = false;

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'message', 'status_code',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

            Text::make('Full_url'),

            Text::make('Path'),

            Text::make('Password'),

            Text::make('Status_code'),

            Text::make('Line'),

            Text::make('File'),

            Text::make('Message'),

            Text::make('Trace'),

            Text::make('User_id'),

            Text::make('Prevoius_url'),

            Text::make('App_name'),

            Text::make('Debug_mode'),

            Text::make('Ip_address'),

            Text::make('Headers'),
        ];

    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }

    /**
     * Determine if the current user can update the given resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public function authorizedToUpdate(Request $request)
    {
        return false;
    }

    /**
     * Determine if the current user can create the given resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public static function authorizedToCreate(Request $request)
    {
        return false;
    }
}

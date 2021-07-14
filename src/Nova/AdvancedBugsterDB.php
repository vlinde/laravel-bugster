<?php

namespace Vlinde\Bugster\Nova;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Resource;
use Vlinde\Bugster\Models\AdvancedBugsterDB as AdvancedBugsterDBModel;

class AdvancedBugsterDB extends Resource
{
    public static $displayInNavigation = false;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = AdvancedBugsterDBModel::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'status_code';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'message', 'status_code', 'path', 'file', 'ip_address', 'app_name',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

            Text::make('Category')
                ->sortable(),

            Text::make('Type')
                ->sortable(),

            Text::make('Status Code')
                ->sortable(),

            Text::make('Path'),

            Text::make('File')
                ->hideFromIndex(),

            Text::make('Method')
                ->hideFromIndex(),

            Text::make('Line')
                ->sortable()
                ->hideFromIndex(),

            Text::make('Message')
                ->sortable()
                ->hideFromIndex(),

            Text::make('Message')
                ->displayUsing(function ($value) {
                    return Str::limit($value, 50);
                })
                ->onlyOnIndex(),

            Textarea::make('Trace'),

            Text::make('User Id')
                ->sortable()
                ->hideFromIndex(),

            Text::make('Previous URL', 'previous_url')
                ->hideFromIndex(),

            Text::make('App Name')
                ->hideFromIndex(),

            Text::make('Debug Mode')
                ->sortable()
                ->hideFromIndex(),

            Text::make('IP Address', 'ip_address')
                ->sortable()
                ->hideFromIndex(),

            Text::make('Headers')
                ->hideFromIndex(),

            Text::make('Date')
                ->displayUsing(function ($value) {
                    return "$this->date $this->hour";
                })
                ->hideFromIndex(),
        ];
    }

    /**
     * Determine if the current user can update the given resource.
     *
     * @param Request $request
     * @return bool
     */
    public function authorizedToUpdate(Request $request)
    {
        return false;
    }

    /**
     * Determine if the current user can create the given resource.
     *
     * @param Request $request
     * @return bool
     */
    public static function authorizedToCreate(Request $request)
    {
        return false;
    }
}

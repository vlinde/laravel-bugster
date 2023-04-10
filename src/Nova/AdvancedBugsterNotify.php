<?php

namespace Vlinde\Bugster\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Resource;
use Vlinde\Bugster\Models\AdvancedBugsterNotify as AdvancedBugsterNotifyModel;
use Vlinde\NovaStatistics\Models\Statistic;

class AdvancedBugsterNotify extends Resource
{
    public static $displayInNavigation = false;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = AdvancedBugsterNotifyModel::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(Request $request)
    {
        $statistics = Statistic::select('id', 'name')->groupBy('name')->pluck('name', 'name')->toArray();

        return [
            ID::make()->sortable(),

            Select::make('Statistic', 'statistic_key')
                ->options($statistics)
                ->rules(['required', 'unique:laravel_bugster_notifications,statistic_key']),

            Number::make('Min Value')
                ->rules(['required']),
        ];
    }
}

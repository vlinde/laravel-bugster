<?php

namespace Vlinde\Bugster\Nova;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;
use Vlinde\Bugster\Models\AdvancedBugsterStat as AdvancedBugsterStatModel;

class AdvancedBugsterStat extends Resource
{
    const DEFAULT_INDEX_ORDER = 'daily';

    public static $displayInNavigation = false;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = AdvancedBugsterStatModel::class;

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
        'id', 'category', 'error', 'file',
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
            ID::make()
                ->sortable(),

            Text::make("Error")
                ->hideFromIndex(),

            Text::make("Error")
                ->displayUsing(function ($value) {
                    return Str::limit($value, 50);
                })
                ->sortable()
                ->hideFromDetail(),

            Text::make("Category")
                ->sortable(),

            Text::make("File")
                ->hideFromIndex(),

            Number::make("Daily")
                ->sortable(),

            Number::make("Weekly")
                ->sortable(),

            Number::make("Monthly")
                ->sortable(),

            BelongsToMany::make("Advanced Bugster Db", 'bugs'),
        ];
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        $query->when(empty($request->get('orderBy')), function (Builder $q) {
            $q->getQuery()->orders = [];

            return $q->orderBy(static::DEFAULT_INDEX_ORDER, 'desc');
        });
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

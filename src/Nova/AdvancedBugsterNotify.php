<?php

namespace Vlinde\Bugster\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Resource;
use Vlinde\Bugster\Models\AdvancedBugsterNotify as AdvancedBugsterNotifyModel;

class AdvancedBugsterNotify extends Resource
{
    public static $displayInNavigation = false;

    public static $globallySearchable = false;

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
    public static $title = 'statistic_key';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'statistic_key',
    ];

    /**
     * Get the fields displayed by the resource.
     */
    public function fields(Request $request): array
    {
        return [
            ID::make()
                ->sortable(),

            Select::make('Statistic', 'statistic_key')
                ->searchable()
                ->options($this->getStatisticsKeys())
                ->rules(['required', 'string']),

            Number::make('Min Value')
                ->nullable()
                ->rules(['nullable', 'numeric']),

            Number::make('Max Value')
                ->nullable()
                ->rules(['nullable', 'numeric']),
        ];
    }

    private function getStatisticsKeys(): array
    {
        $statisticsKeys = config('bugster.statistic_keys', []);

        return array_combine($statisticsKeys, $statisticsKeys);
    }
}

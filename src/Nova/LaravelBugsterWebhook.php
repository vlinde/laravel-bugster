<?php

namespace Vlinde\Bugster\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Resource;
use Vlinde\Bugster\Nova\Actions\TestWebhook;

class LaravelBugsterWebhook extends Resource
{
    public static $displayInNavigation = false;

    public static $globallySearchable = false;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Vlinde\Bugster\Models\LaravelBugsterWebhook::class;

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
        'id', 'url',
    ];

    public static function label(): string
    {
        return 'Webhooks';
    }

    /**
     * Get the fields displayed by the resource.
     */
    public function fields(Request $request): array
    {
        return [
            ID::make()
                ->sortable(),

            Text::make('Type')
                ->rules(['nullable', 'string']),

            Text::make('Url')
                ->rules(['required', 'string', 'url']),

            Code::make('Payload')
                ->json()
                ->rules(['required', 'json'])
                ->help('Define the payload to be sent to the webhook URL. Available variables: {{title}}, {{message}}'),

            Boolean::make('Active')
                ->sortable(),
        ];
    }

    public function actions(Request $request): array
    {
        return [
            new TestWebhook,
        ];
    }
}

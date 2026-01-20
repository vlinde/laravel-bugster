<?php

namespace Vlinde\Bugster\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Vlinde\Bugster\Jobs\SendWebhookNotification;

class TestWebhook extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     *
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $model) {
            SendWebhookNotification::dispatchSync([
                'title' => 'Test title',
                'message' => 'Test message',
            ], $model->id, false);
        }
    }
}

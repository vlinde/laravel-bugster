<?php

namespace Vlinde\Bugster\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\MicrosoftTeams\Actions\ActionOpenUrl;
use NotificationChannels\MicrosoftTeams\ContentBlocks\TextBlock;
use NotificationChannels\MicrosoftTeams\MicrosoftTeamsAdaptiveCard;
use NotificationChannels\MicrosoftTeams\MicrosoftTeamsChannel;

class QueuesStoppedWorking extends Notification
{
    use Queueable;

    /**
     * @var array
     */
    protected $stoppedQueues;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $stoppedQueues)
    {
        $this->stoppedQueues = $stoppedQueues;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     */
    public function via($notifiable): array
    {
        return [MicrosoftTeamsChannel::class];
    }

    public function toMicrosoftTeams($notifiable): MicrosoftTeamsAdaptiveCard
    {
        $this->stoppedQueues = array_map(function ($stoppedQueue) {
            return "**$stoppedQueue**";
        }, $this->stoppedQueues);

        $content = [
            TextBlock::create()
                ->setText('The following queue(s) do not work: '.implode(', ', $this->stoppedQueues)),
            TextBlock::create()
                ->setText('Restart them from Forge'),
        ];

        $actions = [
            ActionOpenUrl::create()
                ->setUrl('https://forge.laravel.com/servers')
                ->setTitle('Forge'),
        ];

        return MicrosoftTeamsAdaptiveCard::create()
            ->to(config('bugster.microsoft_team_hook'))
            ->title('Queues have stopped working')
            ->content($content)
            ->actions($actions);
    }
}

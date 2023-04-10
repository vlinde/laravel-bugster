<?php

namespace Vlinde\Bugster\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\MicrosoftTeams\MicrosoftTeamsChannel;
use NotificationChannels\MicrosoftTeams\MicrosoftTeamsMessage;

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

    public function toMicrosoftTeams($notifiable)
    {
        $this->stoppedQueues = array_map(function ($stoppedQueue) {
            return "**$stoppedQueue**";
        }, $this->stoppedQueues);

        return MicrosoftTeamsMessage::create()
            ->to(config('bugster.microsoft_team_hook'))
            ->type('warning')
            ->title('Queues have stopped working')
            ->content('The following queue(s) do not work: '.implode(', ', $this->stoppedQueues), ['section' => 1])
            ->content('Restart them from Forge', ['section' => 2])
            ->button('Forge', 'https://forge.laravel.com/servers');
    }
}

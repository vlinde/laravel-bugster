<?php

namespace Vlinde\Bugster\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\MicrosoftTeams\Actions\ActionOpenUrl;
use NotificationChannels\MicrosoftTeams\ContentBlocks\TextBlock;
use NotificationChannels\MicrosoftTeams\MicrosoftTeamsAdaptiveCard;
use NotificationChannels\MicrosoftTeams\MicrosoftTeamsChannel;

class InvalidStatistics extends Notification
{
    use Queueable;

    private string $message;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return [MicrosoftTeamsChannel::class];
    }

    public function toMicrosoftTeams($notifiable): MicrosoftTeamsAdaptiveCard
    {
        $content = [
            TextBlock::create()
                ->setText($this->message),
        ];

        $actions = [
            ActionOpenUrl::create()
                ->setUrl(url('/nova/dashboards/main'))
                ->setTitle('Check Statistics'),
        ];

        return MicrosoftTeamsAdaptiveCard::create()
            ->to(config('bugster.microsoft_team_hook'))
            ->title('Statistics')
            ->content($content)
            ->actions($actions);
    }
}

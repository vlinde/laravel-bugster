<?php

namespace Vlinde\Bugster\Notifications;

use Illuminate\Bus\Queueable;
use NotificationChannels\MicrosoftTeams\MicrosoftTeamsChannel;
use NotificationChannels\MicrosoftTeams\MicrosoftTeamsMessage;
use Illuminate\Notifications\Notification;

class InvalidStatistics extends Notification
{
    use Queueable;

    /**
     * @var string
     */
    private $statisticKey;

    /**
     * @var int
     */
    private $minValue;

    /**
     * @var int
     */
    private $currentValue;

    /**
     * Create a new notification instance.
     *
     * @param string $statisticKey
     * @param int $minValue
     * @param int $currentValue
     * @return void
     */
    public function __construct(string $statisticKey, int $minValue, int $currentValue)
    {
        $this->statisticKey = $statisticKey;
        $this->minValue = $minValue;
        $this->currentValue = $currentValue;
    }

    public function via($notifiable)
    {
        return [MicrosoftTeamsChannel::class];
    }

    public function toMicrosoftTeams($notifiable)
    {
        return MicrosoftTeamsMessage::create()
            ->to(config('bugster.microsoft_team_hook'))
            ->type('warning')
            ->title('Statistics')
            ->content("Stats for '$this->statisticKey' ($this->currentValue) is less than $this->minValue")
            ->button('Check Statistics', url('/nova/dashboards/main'));

    }
}

<?php

namespace Vlinde\Bugster\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use NotificationChannels\MicrosoftTeams\MicrosoftTeamsChannel;
use Vlinde\Bugster\Jobs\SendWebhookNotification;
use Vlinde\Bugster\Models\AdvancedBugsterNotify;
use Vlinde\Bugster\Notifications\InvalidStatistics;
use Vlinde\NovaStatistics\Models\Statistic;

class NotifyStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bugster:notify:statistics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if statuses is less than min value of them';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $bugestStats = AdvancedBugsterNotify::select('statistic_key', 'min_value', 'max_value')->get();

        $statistics = Statistic::select('name', 'value')
            ->whereIn('name', $bugestStats->pluck('statistic_key')->toArray())
            ->whereDate('created_at', Carbon::today())
            ->groupBy('name')
            ->pluck('value', 'name')
            ->map(function ($statistic) {
                return (int) $statistic;
            })
            ->toArray();

        foreach ($bugestStats as $bugestStat) {
            if (! array_key_exists($bugestStat->statistic_key, $statistics)) {
                continue;
            }

            $statValue = $statistics[$bugestStat->statistic_key];

            if ($bugestStat->min_value !== null && $bugestStat->max_value !== null &&
                ($statValue < $bugestStat->min_value || $statValue > $bugestStat->max_value)
            ) {
                $message = "Stats for '$bugestStat->statistic_key' ($statValue) is not between '$bugestStat->min_value-$bugestStat->max_value' range";
            } elseif ($bugestStat->min_value !== null && $statValue < $bugestStat->min_value) {
                $message = "Stats for '$bugestStat->statistic_key' ($statValue) is less than '$bugestStat->min_value'";
            } elseif ($bugestStat->max_value !== null && $statValue > $bugestStat->max_value) {
                $message = "Stats for '$bugestStat->statistic_key' ($statValue) is more than '$bugestStat->max_value'";
            } else {
                continue;
            }

            SendWebhookNotification::dispatchSync([
                'title' => 'Statistics',
                'message' => $message,
            ]);

            //            Notification::route(MicrosoftTeamsChannel::class, null)
            //                ->notify(new InvalidStatistics($message));
        }
    }
}

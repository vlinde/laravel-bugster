<?php

namespace Vlinde\Bugster\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
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
        $bugestStats = AdvancedBugsterNotify::select('statistic_key', 'min_value')
            ->pluck('min_value', 'statistic_key')
            ->toArray();

        $statistics = Statistic::select('name', 'value')
            ->whereIn('name', array_keys($bugestStats))
            ->whereDate('created_at', Carbon::today())
            ->groupBy('name')
            ->pluck('value', 'name')
            ->map(function ($statistic) {
                return (int) $statistic;
            })
            ->toArray();

        foreach ($bugestStats as $key => $minValue) {
            if (! array_key_exists($key, $statistics)) {
                continue;
            }

            if ($statistics[$key] >= $minValue) {
                continue;
            }

            (new User)
                ->forceFill([
                    'name' => 'Microsoft Teams',
                    'email' => 'dev@vlinde.com',
                ])
                ->notify(new InvalidStatistics($key, $minValue, $statistics[$key]));
        }
    }
}

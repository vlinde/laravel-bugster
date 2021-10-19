<?php

namespace Vlinde\Bugster\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Vlinde\Bugster\Models\AdvancedBugsterDB;
use Vlinde\Bugster\Models\AdvancedBugsterStat;

class DeleteOldBugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bugster:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete old logs';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $subMonth = Carbon::now()->subMonth();

        AdvancedBugsterDB::select('id')
            ->whereDate('created_at', '<', $subMonth)
            ->chunkById(1000, function ($logs) {
                foreach ($logs as $log) {
                    $log->stats()->detach();
                }

                $logsId = $logs->pluck('id')->toArray();

                AdvancedBugsterDB::whereIn('id', $logsId)->delete();
            });

        AdvancedBugsterStat::select('id')
            ->whereDate('created_at', '<', $subMonth)
            ->chunkById(1000, function ($stats) {
                $statsId = $stats->pluck('id')->toArray();

                AdvancedBugsterStat::whereIn('id', $statsId)->delete();
            });
    }
}

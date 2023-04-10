<?php

namespace Vlinde\Bugster\Console\Commands;

use Illuminate\Console\Command;
use Vlinde\Bugster\Models\AdvancedBugsterDB;

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
     */
    public function handle(): void
    {
        $subMonth = now()->subMonth();

        AdvancedBugsterDB::select('id')
            ->whereDate('created_at', '<', $subMonth)
            ->chunkById(1000, function ($logs) {
                foreach ($logs as $log) {
                    $log->stats()->detach();
                }

                $logsId = $logs->pluck('id')->toArray();

                AdvancedBugsterDB::whereIn('id', $logsId)->delete();
            });
    }
}

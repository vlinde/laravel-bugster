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

        $oldLogs = AdvancedBugsterDB::select('id')->whereDate('created_at', '<', $subMonth)->get();

        foreach ($oldLogs as $oldLog) {
            $oldLog->stats()->detach();
        }

        AdvancedBugsterDB::select('id')->whereDate('created_at', '<', $subMonth)->delete();
        AdvancedBugsterStat::select('id')->whereDate('created_at', '<', $subMonth)->delete();
    }
}

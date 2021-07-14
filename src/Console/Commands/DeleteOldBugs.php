<?php

namespace Vlinde\Bugster\Console\Commands;

use Carbon\Carbon;
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
     *
     * @return void
     */
    public function handle(): void
    {
        AdvancedBugsterDB::whereDate('created_at', '<', Carbon::now()->subMonths())->delete();
    }
}

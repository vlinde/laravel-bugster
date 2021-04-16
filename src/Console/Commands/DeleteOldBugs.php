<?php

namespace Vlinde\Bugster\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Vlinde\Bugster\Models\AdvancedBugsterDB;
use Vlinde\Bugster\Models\AdvancedBugsterLink;

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
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $this->deleteErrors();
        }
        catch (\Exception $ex) {
        }
    }

    public function deleteErrors() {
        $errors = AdvancedBugsterDB::where([['created_at','<',Carbon::today()->subMonths(1)]])->delete();
        $errors = AdvancedBugsterLink::where([['generated_at','<',Carbon::today()->subMonths(1)]])->delete();
    }


}

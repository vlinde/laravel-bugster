<?php

namespace Vlinde\Bugster\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Vlinde\Bugster\Models\AdvancedBugsterDB;
use Vlinde\Bugster\Models\AdvancedBugsterLink;
use Vlinde\Bugster\Models\AdvancedBugsterStat;

class GenerateStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bugster:generate:stats';

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
            $this->groupBugs();
        } catch (\Exception $ex) {
        }
    }

    public function groupBugs()
    {
        foreach (AdvancedBugsterDB::get() as $bugs) {
            if (!AdvancedBugsterStat::where("error", $bugs->message)->exists()) {
                $newErrorStat = new AdvancedBugsterStat();
                $newErrorStat->error = $bugs->message;
                $newErrorStat->category = $bugs->category;
                $newErrorStat->file = $bugs->file;
                $newErrorStat->generated_at = Carbon::now();

                $newErrorStat->save();
            }
        }

//        $dailyErrors = AdvancedBugsterDB::where([
//            ['created_at', '<', Carbon::now()],
//            ['created_at', '>', Carbon::now()->subDay()]
//        ])->get();
//
//        $weeklyErrors = AdvancedBugsterDB::where([
//            ['created_at', '<', Carbon::now()->subDay()],
//            ['created_at', '>', Carbon::now()->subWeek()]
//        ])->get();
//
//        $monthlyErrors = AdvancedBugsterDB::where([
//            ['created_at', '<', Carbon::now()->subWeek()],
//            ['created_at', '>', Carbon::now()->subMonth()]
//        ])->get();

        foreach (AdvancedBugsterStat::get() as $stats) {
            $stats->daily = AdvancedBugsterDB::where([
                ['created_at', '<', Carbon::now()],
                ['created_at', '>', Carbon::now()->subDay()],
                ['message', '=', $stats->error],
            ])->count();

            $stats->weekly = AdvancedBugsterDB::where([
                ['created_at', '<', Carbon::now()],
                ['created_at', '>', Carbon::now()->subWeek()],
                ['message', '=', $stats->error],
            ])->count();

            $monthlyerrors = AdvancedBugsterDB::where([
                ['created_at', '<', Carbon::now()],
                ['created_at', '>', Carbon::now()->subMonth()],
                ['message', '=', $stats->error],
            ])->get();

            $errorIds = [];
            foreach ($monthlyerrors as $monthlyerror) $errorIds[] = $monthlyerror->id;

            $stats->monthly = count($monthlyerrors);

            $stats->save();

            $stats->bugs()->sync($errorIds);

            $stats->save();
        }
    }
}

<?php

namespace Vlinde\Bugster\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Vlinde\Bugster\Models\AdvancedBugsterDB;
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
    protected $description = 'Generate stats for logs';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->groupBugs();
    }

    public function groupBugs(): void
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

        foreach (AdvancedBugsterStat::get() as $stats) {
            $stats->daily = AdvancedBugsterDB::whereDate('created_at', '<', Carbon::now())
                ->whereDate('created_at', '>', Carbon::now()->subDay())
                ->where('message', $stats->error)
                ->count();

            $stats->weekly = AdvancedBugsterDB::whereDate('created_at', '<', Carbon::now())
                ->whereDate('created_at', '>', Carbon::now()->subWeek())
                ->where('message', $stats->error)
                ->count();

            $monthlyerrors = AdvancedBugsterDB::whereDate('created_at', '<', Carbon::now())
                ->whereDate('created_at', '>', Carbon::now()->subMonth())
                ->where('message', $stats->error)
                ->get();

            $errorIds = [];

            foreach ($monthlyerrors as $monthlyerror) {
                $errorIds[] = $monthlyerror->id;
            }

            $stats->monthly = count($monthlyerrors);

            $stats->save();

            $stats->bugs()->sync($errorIds);

            $stats->save();
        }
    }
}

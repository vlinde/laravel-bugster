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
        $dailyErrors = AdvancedBugsterDB::where([
            ['created_at', '<', Carbon::now()],
            ['created_at', '>', Carbon::now()->subDay()]
        ])->get();

        $weeklyErrors = AdvancedBugsterDB::where([
            ['created_at', '<', Carbon::now()],
            ['created_at', '>', Carbon::now()->subWeek()]
        ])->get();

        $monthlyErrors = AdvancedBugsterDB::where([
            ['created_at', '<', Carbon::now()],
            ['created_at', '>', Carbon::now()->subMonth()]
        ])->get();

        foreach ($dailyErrors as $value) {
            if (!AdvancedBugsterStat::where("category", "daily")->exists()) {
                $newStat = new AdvancedBugsterStat();
            } else {
                $newStat = AdvancedBugsterStat::where("category", "daily")->first();
            }
                $newStat->generated_at = Carbon::now();
                $newStat->category = 'daily';
                $newStat->error_count = count($dailyErrors);
                try {
                    $newStat->save();
                } catch (\Exception $ex) {
                    Log::error($ex->getMessage());
                }

                if (!$newStat->bugs->contains($value->id)) {
                    $newStat->bugs()->attach([$value->id]);
                }
                $newStat->save();
        }

        foreach ($weeklyErrors as $value) {
            if (!AdvancedBugsterStat::where("category", "weekly")->exists()) {
                $newStat = new AdvancedBugsterStat();
            } else {
                $newStat = AdvancedBugsterStat::where("category", "weekly")->first();
            }
                $newStat->generated_at = Carbon::now();
                $newStat->category = 'weekly';
                $newStat->error_count = count($weeklyErrors);
                try {
                    $newStat->save();
                } catch (\Exception $ex) {
                    Log::error($ex->getMessage());
                }

                if (!$newStat->bugs->contains($value->id)) {
                    $newStat->bugs()->attach([$value->id]);
                }
                $newStat->save();
        }

        foreach ($monthlyErrors as $value) {
            if (!AdvancedBugsterStat::where("category", "monthly")->exists()) {
                $newStat = new AdvancedBugsterStat();
            } else {
                $newStat = AdvancedBugsterStat::where("category", "monthly")->first();
            }
                $newStat->generated_at = Carbon::now();
                $newStat->category = 'monthly';
                $newStat->error_count = count($monthlyErrors);
                try {
                    $newStat->save();
                } catch (\Exception $ex) {
                    Log::error($ex->getMessage());
                }

                if (!$newStat->bugs->contains($value->id)) {
                    $newStat->bugs()->attach([$value->id]);
                }
                $newStat->save();
        }
    }

}

<?php

namespace Vlinde\Bugster\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
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
        }
        catch (\Exception $ex) {
        }
    }

    public function groupBugs() {

        $daily = [];
        $weekly = [];
        $monthly = [];
        $dailyErrors = AdvancedBugsterDB::where([
            ['created_at','<',Carbon::now()],
            ['created_at','>',Carbon::now()->subDay()]
        ])->get();

        $weeklyErrors = AdvancedBugsterDB::where([
            ['created_at', '<', Carbon::now()],
            ['created_at', '>', Carbon::now()->subWeek()]
        ])->get();

        $monthlyErrors = AdvancedBugsterDB::where([
            ['created_at', '<', Carbon::now()],
            ['created_at', '>', Carbon::now()->subMonth()]
        ])->get();

        foreach ($dailyErrors as $error) {
            if(!isset($daily[$error->message]['count'])) $daily[$error->message]['count'] = 0;

            $daily[$error->message]['count']++;
            $daily[$error->message]['file'] = $error->file;
            $daily[$error->message]['link'][] = $error->links()->first()->id;
        }

        foreach ($weeklyErrors as $error) {
            if(!isset($weekly[$error->message]['count'])) $weekly[$error->message]['count'] = 0;

            $weekly[$error->message]['count']++;
            $weekly[$error->message]['file'] = $error->file;
            $weekly[$error->message]['link'][] = $error->links()->first()->id;
        }

        foreach ($monthlyErrors as $error) {
            if(!isset($monthly[$error->message]['count'])) $monthly[$error->message]['count'] = 0;

            $monthly[$error->message]['count']++;
            $monthly[$error->message]['file'] = $error->file;
            $monthly[$error->message]['link'][] = $error->links()->first()->id;
        }

        foreach ($daily as $key => $value) {
            $newStat = new AdvancedBugsterStat();

            $newStat->generated_at = Carbon::now();
            $newStat->category = 'daily';
            $newStat->error = $key;
            $newStat->error_count = $value['count'];
            $newStat->file = $value['file'];
            try {
                $newStat->save();
            } catch (\Exception $ex) {
            }

            foreach ($value['link'] as $links) {
                if(!$newStat->links->contains($links)) {
                    $newStat->links()->attach([$links]);
                }
            }
            $newStat->save();
        }

        foreach ($weekly as $key => $value) {
            $newStat = new AdvancedBugsterStat();

            $newStat->generated_at = Carbon::now();
            $newStat->category = 'weekly';
            $newStat->error = $key;
            $newStat->error_count = $value['count'];
            $newStat->file = $value['file'];
            try {
                $newStat->save();
            } catch (\Exception $ex) {
            }

            foreach ($value['link'] as $links) {
                if(!$newStat->links->contains($links)) {
                    $newStat->links()->attach([$links]);
                }
            }
            $newStat->save();
        }

        foreach ($monthly as $key => $value) {
            $newStat = new AdvancedBugsterStat();

            $newStat->generated_at = Carbon::now();
            $newStat->category = 'monthly';
            $newStat->error = $key;
            $newStat->error_count = $value['count'];
            $newStat->file = $value['file'];
            try {
                $newStat->save();
            } catch (\Exception $ex) {
            }

            foreach ($value['link'] as $links) {
                if(!$newStat->links->contains($links)) {
                    $newStat->links()->attach([$links]);
                }
            }
            $newStat->save();
        }
    }

}

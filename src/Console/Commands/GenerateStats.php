<?php

namespace Vlinde\Bugster\src\Console\Commands\GenerateStats;

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
        $errorArray = [
            '100','101',
            '300','201','202','203','204','205',
            '300','301','302','303','304','305',
            '400','401','402','403','404','405','406','407','408','409','410','411','412','413','414','415',
            '500','501','502','503','504','505'
        ];

        $overallErrorCount = [];

        foreach ( $errorArray as $currentError) {

            $DBErrorsCount = AdvancedBugsterDB::where([['created_at', '>', Carbon::now()->subDays(1)]])
                ->where(function ($query) use ($currentError) {
                    return $query->where('path', $currentError)
                        ->orWhere('message', $currentError);
                })->count();

            if( $DBErrorsCount != 0 ) {
                array_push($overallErrorCount, [$currentError => $DBErrorsCount]);
            }
        }

        if( !empty($overallErrorCount) ) {
            $stat = new AdvancedBugsterStat();

            $stat->date = Carbon::today()->toDateString();
            $stat->url_id = null;
            $stat->errors = json_encode($overallErrorCount);
            try {
                $stat->save();
            }
            catch (\Exception $ex) {
                error_log(var_dump($ex));
            }
        }

        foreach ( AdvancedBugsterDB::where([['created_at','>',Carbon::now()->subDays(1)]])->get() as $error) {
            $this->saveLink($error->previous_url);
        }

        foreach( AdvancedBugsterLink::all() as $link ) {

                $DBErrors = AdvancedBugsterDB::select('previous_url', 'path', 'message')
                    ->where([['created_at', '>', Carbon::yesterday()]])
                    ->where('previous_url', $link->url)
                    ->count();

                if ($DBErrors != null) {
                    $stat = new AdvancedBugsterStat();

                    $stat->date = Carbon::today()->toDateString();
                    $stat->url_id = $link->id;
                    $stat->errors = json_encode($DBErrors);
                    try {
                        $stat->save();
                    }
                    catch (\Exception $ex) {
                        error_log($ex);
                    }
                }
            }

    }

    public function showStats($date = 'today') {

    }

    public function saveLink($l) {
        $existingLink = AdvancedBugsterLink::where('url',$l)->first();

        if( $existingLink == null ) {
            $link = new AdvancedBugsterLink();
            $link->url = $l;

            try {
                $link->save();
            }
            catch (\Exception $ex) {
            }
        }
        else {
            $existingLink->last_apparition = Carbon::now();
            $existingLink->save();
        }
    }

}

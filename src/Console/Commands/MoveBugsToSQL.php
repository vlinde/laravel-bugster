<?php

namespace Vlinde\Bugster\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Vlinde\Bugster\Models\AdvancedBugsterDB;
use Vlinde\Bugster\Models\AdvancedBugsterStat;

class MoveBugsToSQL extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bugster:movetosql';

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
            $this->moveData();
        }
        catch (\Exception $ex) {
        }
    }

    public function moveData() {
        $conn = Redis::connection('Bugster');
        $keys = $conn->keys('*');
        foreach ($keys as $key) {
            $currentKey = $conn->get(Str::after($key,'laravel_database_'));
            $this->saveError($currentKey);
        }
    }

    public function saveError($e) {
        $e = json_decode($e);
        $bugsterBug = new AdvancedBugsterDB();

        $bugsterBug->full_url = $e->full_url;
        $bugsterBug->path = $e->path;
        $bugsterBug->method = $e->method;
        $bugsterBug->status_code = $e->status_code;
        $bugsterBug->line = $e->line;
        $bugsterBug->file = $e->file;
        $bugsterBug->message = $e->message;
        $bugsterBug->trace = $e->trace;
        $bugsterBug->user_id = $e->user_id;
        $bugsterBug->previous_url = $e->previous_url;
        $bugsterBug->app_name = $e->app_env;
        $bugsterBug->debug_mode = $e->debug_mode;
        $bugsterBug->ip_address = $e->ip_address;
        $bugsterBug->headers = '';

        try {
            $bugsterBug->save();
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

        $ErrorCountArray = [];

        foreach ( $errorArray as $currentError) {
            $DBErrorsCount = AdvancedBugsterDB::where([['created_at','<',Carbon::today()],['created_at','>',Carbon::yesterday()]])
                ->where(function($query) use ($currentError){
                    return $query->where('path',$currentError)
                              ->orWhere('message',$currentError);
                })->count();

            if( $DBErrorsCount != null ) {
                array_push( $ErrorCountArray, [$currentError => $DBErrorsCount] );
            }
        }

        if( !empty($ErrorCountArray) ) {
            $stat = new AdvancedBugsterStat();

            $stat->date = Carbon::today()->toDateString();
            $stat->errors = $ErrorCountArray;
            try {
                $stat->save();
            }
            catch (\Exception $ex) {

            }
        }

    }

    public function showStats($date = 'today') {

    }

}

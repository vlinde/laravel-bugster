<?php

namespace Vlinde\Bugster\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Vlinde\Bugster\Models\AdvancedBugsterDB;

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
    protected $description = 'Save logs from redis';

    /**
     * Execute the console command.
     *
     * @return void
     * @throws \JsonException
     */
    public function handle(): void
    {
        $conn = Redis::connection(config('bugster.redis_connection_name'));

        $keys = $conn->keys('bugster*');

        foreach ($keys as $key) {
            $currentKey = $conn->get($key);

            $this->saveLog($currentKey);

            $conn->del($key);
        }
    }

    public function saveLog(string $log): void
    {
        $log = json_decode($log, true, 512, JSON_THROW_ON_ERROR);

        $logExists = AdvancedBugsterDB::where('date', $log['date'])
            ->where('hour', $log['hour'])
            ->where('message', $log['message'])
            ->exists();

        if ($logExists) {
            return;
        }

        $bugster = new AdvancedBugsterDB();

        $bugster->full_url = $log['full_url'];
        $bugster->category = "laravel";
        $bugster->type = $log['type'];
        $bugster->path = $log['path'];
        $bugster->method = $log['method'];
        $bugster->status_code = $log['status_code'];
        $bugster->line = $log['line'];
        $bugster->file = $log['file'];
        $bugster->message = $log['message'];
        $bugster->trace = null;
        $bugster->user_id = $log['user_id'];
        $bugster->previous_url = $log['previous_url'];
        $bugster->app_name = $log['app_env'];
        $bugster->debug_mode = $log['debug_mode'];
        $bugster->ip_address = $log['ip_address'];
        $bugster->headers = null;
        $bugster->date = $log['date'];
        $bugster->hour = $log['hour'];

        $bugster->save();
    }
}

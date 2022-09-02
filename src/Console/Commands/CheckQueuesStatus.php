<?php

namespace Vlinde\Bugster\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Vlinde\Bugster\Notifications\QueuesStoppedWorking;

class CheckQueuesStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queues:check-status';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if queues working';

    /**
     * @var string
     */
    private $redisConnection;

    /**
     * @var string
     */
    private $logChannel;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->redisConnection = config('bugster.redis_connection_for_queues_status');
        $this->logChannel = config('bugster.log_channel');

        $queues = $this->getQueues();

        $stoppedQueues = [];

        foreach ($queues as $queue) {
            $output = exec("ps aux | grep 'artisan queue:work.*$this->redisConnection.*--queue=$queue' | grep -v grep");

            if (empty($output)) {
                Log::channel($this->logChannel)->debug("Checked queue '$queue' output: empty");
                $stoppedQueues[] = $queue;

                continue;
            }

            Log::channel($this->logChannel)->debug("Checked queue '$queue' output: $output");
        }

        if (!empty($stoppedQueues)) {
            (new User)
                ->forceFill([
                    'name' => 'Microsoft Teams',
                    'email' => 'dev@vlinde.com',
                ])
                ->notify(new QueuesStoppedWorking($stoppedQueues));
        }

        return self::SUCCESS;
    }

    private function getQueues(): array
    {
        $redis = Redis::connection($this->redisConnection);

        $queuesGroups = $redis->scan('0', [
            'match' => 'queues:*',
            'count' => 10000
        ]);

        $uniqueQueues = [];

        foreach ($queuesGroups as $queuesGroup) {
            if (!is_array($queuesGroup)) {
                continue;
            }

            foreach ($queuesGroup as $queue) {
                $queueName = str_replace(['queues:', ':notify', ':delayed', ':reserved'], '', $queue);

                if (in_array($queueName, $uniqueQueues, true)) {
                    continue;
                }

                $uniqueQueues[] = $queueName;
            }
        }

        return $uniqueQueues;
    }
}

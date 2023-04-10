<?php

namespace Vlinde\Bugster\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
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
     * @var Process
     */
    private $processObject;

    /**
     * @var string
     */
    private $redisConnection;

    /**
     * @var Redis
     */
    private $redis;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->setProcess();

        $outputLines = $this->getPorcessOutputs();

        $queues = $this->getQueues();

        $workingQueues = [];
        foreach ($outputLines as $line) {
            foreach ($queues as $queue) {
                if (Str::containsAll($line, ["artisan queue:work $this->redisConnection", "--queue=$queue"])) {
                    $workingQueues[] = $queue;
                }
            }
        }

        $workingQueues = array_unique($workingQueues);

        $stoppedQueues = array_diff($queues, $workingQueues);

        if (! empty($stoppedQueues)) {
            (new User)
                ->forceFill([
                    'name' => 'Microsoft Teams',
                    'email' => 'dev@vlinde.com',
                ])
                ->notify(new QueuesStoppedWorking($stoppedQueues));
        }

        return self::SUCCESS;
    }

    private function setProcess(): void
    {
        $this->processObject = new Process(['ps', 'aux']);
    }

    private function setRedis(): void
    {
        $this->redisConnection = config('bugster.redis_connection_for_queues_status');
        $this->redis = Redis::connection($this->redisConnection);
    }

    private function getPorcessOutputs(): array
    {
        $this->processObject->run();

        $commandOutput = $this->processObject->getOutput();

        return explode(PHP_EOL, $commandOutput);
    }

    private function getQueues(): array
    {
        $this->setRedis();

        $queuesGroups = $this->redis->scan('0', [
            'match' => 'queues:*',
            'count' => 10000,
        ]);

        $queues = [];

        foreach ($queuesGroups as $queuesGroup) {
            if (! is_array($queuesGroup)) {
                continue;
            }

            foreach ($queuesGroup as $queue) {
                $queueName = str_replace(['queues:', ':notify', ':delayed', ':reserved'], '', $queue);

                $queues[] = $queueName;
            }
        }

        return array_unique($queues);
    }
}

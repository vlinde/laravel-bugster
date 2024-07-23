<?php

namespace Vlinde\Bugster\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Facades\Log;
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

    private Process $processObject;

    private string $redisConnection;

    private Connection $redis;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $this->setProcess();

            $outputLines = $this->getProcessOutputs();

            $queues = $this->getQueues();

            $workingQueues = $this->findWorkingQueues($outputLines, $queues);

            $stoppedQueues = $this->findStoppedQueues($queues, $workingQueues);

            $stoppedQueues = $this->checkJobProcessing($queues, $stoppedQueues);

            $this->notifyIfStopped($stoppedQueues);

            return self::SUCCESS;
        } catch (\Exception $e) {
            Log::error('Error checking queues: '.$e->getMessage());

            return self::FAILURE;
        }
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

    private function getProcessOutputs(): array
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
            if (is_array($queuesGroup)) {
                foreach ($queuesGroup as $queue) {
                    $queueName = str_replace(['queues:', ':notify', ':delayed', ':reserved'], '', $queue);
                    $queues[] = $queueName;
                }
            }
        }

        return array_unique($queues);
    }

    private function findWorkingQueues(array $outputLines, array $queues): array
    {
        $workingQueues = [];

        foreach ($outputLines as $line) {
            foreach ($queues as $queue) {
                if (Str::containsAll($line, ["artisan queue:work $this->redisConnection", "--queue=$queue"])) {
                    $workingQueues[] = $queue;
                }
            }
        }

        return array_unique($workingQueues);
    }

    private function findStoppedQueues(array $queues, array $workingQueues): array
    {
        return array_diff($queues, $workingQueues);
    }

    private function checkJobProcessing(array $queues, array $stoppedQueues): array
    {
        foreach ($queues as $queue) {
            if (in_array($queue, $stoppedQueues, true)) {
                continue;
            }

            $lastJob = $this->getLastJob("queues:$queue");

            if ($lastJob && (int) $lastJob['pushedAt'] < strtotime('-1 day')) {
                $stoppedQueues[] = $queue;

                continue;
            }

            $firstDelayedJob = $this->getFirstDelayedJob("queues:$queue:delayed");

            if ($firstDelayedJob && $firstDelayedJob['delayed_until'] < time()) {
                $stoppedQueues[] = $queue;
            }
        }

        return $stoppedQueues;
    }

    private function getLastJob(string $queue): ?array
    {
        $lastJob = $this->redis->lindex($queue, -1);

        return $lastJob ? json_decode($lastJob, true) : null;
    }

    private function getFirstDelayedJob(string $delayedQueue): ?array
    {
        $delayedJobs = $this->redis->zrangebyscore($delayedQueue, '-inf', '+inf', ['limit' => [0, 1]]);

        if (empty($delayedJobs)) {
            return null;
        }

        $firstDelayedJob = json_decode($delayedJobs[0], true);

        if (isset($firstDelayedJob['data']['command'])) {
            $commandData = unserialize($firstDelayedJob['data']['command'], ['allowed_classes' => true]);

            if (isset($commandData->delay)) {
                $firstDelayedJob['delayed_until'] = $commandData->delay->timestamp;
            }
        }

        return $firstDelayedJob;
    }

    private function notifyIfStopped(array $stoppedQueues): void
    {
        if (! empty($stoppedQueues)) {
            (new User)
                ->forceFill([
                    'name' => 'Microsoft Teams',
                    'email' => 'dev@vlinde.com',
                ])
                ->notify(new QueuesStoppedWorking($stoppedQueues));
        }
    }
}

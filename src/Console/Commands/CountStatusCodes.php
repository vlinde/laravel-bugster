<?php

namespace Vlinde\Bugster\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Vlinde\Bugster\Models\LaravelBugsterStatusCode;

class CountStatusCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bugster:count-status-codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Count status codes from bugser log file';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $codes = [];

        $this->readLogFile(function ($logLine) use (&$codes) {
            if (! preg_match('/code:\s*(\d+)/', $logLine, $matches)) {
                return;
            }

            $code = $matches[1];

            if (! isset($codes[$code])) {
                $codes[$code] = 1;

                return;
            }

            $codes[$code]++;
        });

        foreach ($codes as $code => $count) {
            LaravelBugsterStatusCode::firstOrCreate([
                'code' => (int) $code,
                'date' => now()->subDay()->format('Y-m-d'),
            ], [
                'display_name' => Response::$statusTexts[$code] ?? 'Unknown',
                'count' => $count,
            ]);
        }

        return self::SUCCESS;
    }

    private function readLogFile(callable $callback): void
    {
        if (! $logFilePath = $this->getLogFilePath()) {
            return;
        }

        $logFile = fopen($logFilePath, 'rb');

        while (! feof($logFile)) {
            $logLine = fgets($logFile);
            $callback($logLine);
        }

        fclose($logFile);
    }

    private function getLogFilePath(): ?string
    {
        $logChannel = config('bugster.log_channel');
        $logPath = config("logging.channels.$logChannel.path");

        if (! $logPath) {
            return null;
        }

        $yesterdayDate = now()->subDay()->format('Y-m-d');

        $yesterdayLogFile = str_replace('.log', "-$yesterdayDate.log", $logPath);

        if (File::missing($yesterdayLogFile)) {
            return null;
        }

        return $yesterdayLogFile;
    }
}

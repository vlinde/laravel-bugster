<?php

namespace Vlinde\Bugster\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Vlinde\Bugster\Models\AdvancedBugsterDB;

class ParseLogs extends Command
{
    private const LOGS_PATTERN = '/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}([\+-]\d{4})?\].*/';
    private const CURRENT_LOG_PATTERN_1 = '/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}([\+-]\d{4})?)\](?:.*?(\w+)\.|.*?)';
    private const CURRENT_LOG_PATTERN_2 = ': (.*?)( in .*?:[0-9]+)?$/i';
    private const ALLOWED_LEVELS = ['error'];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bugster:generate:errors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Save in the DB logs from folders';

    /**
     * @var array
     */
    protected $files = [];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->iterateThroughDirectory(storage_path('logs'));

        foreach ($this->files as $category => $errors) {
            foreach ($errors as $error) {
                $this->parseLogContents($error['path'], $error['name'], $category);
            }
        }

        if (config('bugster.enable_custom_log_paths') === true) {

            $customFiles = config('bugster.log_paths');

            foreach ($customFiles as $category => $args) {
                if (!$this->validateFile($args['path'], $args['file'])) {
                    continue;
                }

                $this->parseLogContents($args['path'], $args['file'], $category);
            }
        }
    }

    private function parseLogContents(string $path, string $currentFile, string $category): void
    {
        $logs = [];
        $startDate = Carbon::yesterday()->startOfDay();
        $endDate = Carbon::yesterday()->endOfDay();

        $fileContent = file_get_contents($path . '/' . $currentFile);

        preg_match_all(self::LOGS_PATTERN, $fileContent, $headings);

        if (!is_array($headings)) {
            return;
        }

        $log_data = preg_split(self::LOGS_PATTERN, $fileContent);

        if ($log_data[0] < 1) {
            array_shift($log_data);
        }

        foreach ($headings as $h) {
            for ($i = 0, $j = count($h); $i < $j; $i++) {
                foreach (self::ALLOWED_LEVELS as $level) {
                    if (stripos($h[$i], '.' . $level) || stripos($h[$i], $level . ':')) {

                        preg_match(self::CURRENT_LOG_PATTERN_1 . $level . self::CURRENT_LOG_PATTERN_2, $h[$i], $current);

                        if (!isset($current[4])) {
                            continue;
                        }

                        $date = Carbon::parse($current[1]);

                        if (!$date->between($startDate, $endDate)) {
                            continue;
                        }

                        $logs[] = array(
                            'context' => $current[3],
                            'level' => $level,
                            'text' => trim($current[4]),
                            'stack' => preg_replace("/^\n*/", '', $log_data[$i]),
                            'date' => trim($current[1])
                        );
                    }
                }
            }
        }

        $logs = array_reverse($logs);

        foreach ($logs as $log) {
            [$date, $hour] = explode(' ', $log['date']);

            $this->saveError([
                'category' => $category,
                'type' => $log['level'],
                'error' => $log['text'],
                'stacktrace' => null,
                'context' => $log['context'],
                'hour' => trim($hour),
                'date' => trim($date)
            ]);
        }
    }

    private function saveError(array $args): void
    {
        $existingError = AdvancedBugsterDB::where('date', $args['date'])
            ->where('hour', $args['hour'])
            ->where('message', $args['error'])
            ->exists();

        if (!$existingError) {
            $newError = new AdvancedBugsterDB();

            $args['category'] = $args['category'] === 'logs' ? 'laravel' : $args['category'];

            $newError->category = $args['category'];
            $newError->type = $args['type'];
            $newError->full_url = 'parsed_log';
            $newError->path = 'log';
            $newError->status_code = $args['type'] === 'error' ? 500 : null;
            $newError->file = $args['category'];
            $newError->message = $args['error'];
            $newError->trace = $args['stacktrace'];
            $newError->app_name = config('env.APP_NAME');
            $newError->debug_mode = $args['context'];
            $newError->date = $args['date'];
            $newError->hour = $args['hour'];

            $newError->save();
        }
    }

    private function iterateThroughDirectory(string $path): void
    {
        $dir = scandir($path);

        foreach ($dir as $file) {
            if (strpos($file, '.log')) {
                $aux = $path;
                $array1 = explode('/', $aux);
                $array = array_pop($array1);
                $this->files[$array][] = ['path' => $path, 'name' => $file];
            } elseif (strpos($file, '.') >= 0 && strrpos($file, '.') !== false) {
                continue;
            } else {
                $this->iterateThroughDirectory($path . '/' . $file);
            }
        }
    }

    private function validateFile(string $path, string $file): bool
    {
        return is_file($path . '/' . $file);
    }
}

<?php


namespace Vlinde\Bugster\Console\Commands;

use Carbon\Carbon;
use Cassandra\Cluster\Builder;
use Illuminate\Console\Command;
use Vlinde\Bugster\Models\AdvancedBugsterDB;

class ParseLogs extends Command
{
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
    protected $description = 'Command description';

    protected $searchDate;
    protected $searchDateNgnix;
    protected $searchDatePlus;
    protected $searchDatePlusNgnix;

    protected $errorLogs = [];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->searchDate = Carbon::yesterday()->format('Y-m-d');
        $this->searchDatePlus = Carbon::today()->format('Y-m-d');

        $this->searchDateNgnix = Carbon::yesterday()->format('Y/m/d');
        $this->searchDatePlusNgnix = Carbon::today()->format('Y/m/d');
        parent::__construct();
    }

    protected $files;

    protected $errors;

    protected const STACKTRACE = ["[stacktrace]", "Stack trace:"];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->iterateThroughDirectory(storage_path('logs'));

        foreach ($this->errorLogs as $category => $errors) {
            foreach ($errors as $error) {
                if ($category != 'ngnix') {
                    $this->parseLogContents($error['path'], $error['name'], $category, $this->searchDate, $this->searchDatePlus);
                } else {
                    $this->parseLogContents($error['path'], $error['name'], $category, $this->searchDateNgnix, $this->searchDatePlusNgnix);
                }
            }
        }

        if (config('bugster.enable_custom_log_paths') == true) {

            $this->files = config('bugster.log_paths');

            foreach ($this->files as $category => $args) {
                if (!$this->validateFile($args['path'], $args['file'])) {
                    $this->errors[$category] = "Category: " . $category . " does not contain the log file specified here: " . $args['path'] . "/" . $args['file'];
                    continue;
                } else {
                    if ($category != 'ngnix') {
                        $this->parseLogContents($args['path'], $args['file'], $category, $this->searchDate, $this->searchDatePlus);
                    } else {
                        $this->parseLogContents($args['path'], $args['file'], $category, $this->searchDateNgnix, $this->searchDatePlusNgnix);
                    }
                }
            }

            $this->showFileErrors();
        }
    }

    private function parseLogContents($path, $currentFile, $category, $searchDate, $searchDatePlus)
    {
        $file = file_get_contents($path . '/' . $currentFile);

        if (!strpos($file, $searchDate)) {
            return false;
        } else {
            $first_occurence = strpos($file, $searchDate);
            $second_occurence = strpos($file, $searchDate, $first_occurence + 9);
            $late_occurence = strpos($file, $searchDatePlus);
            if (!$second_occurence) {
                if (!$late_occurence) {
                    $error = substr($file, $first_occurence);
                } else {
                    $error = substr($file, $first_occurence, $late_occurence - $first_occurence - 1);
                }
            } else {
                while ($second_occurence) {
                    $currentError = substr($file, $first_occurence, $second_occurence - $first_occurence - 1);
                    $stackTraceLocation = !strpos($currentError, self::STACKTRACE[1]) ? strpos($currentError, self::STACKTRACE[0]) : strpos($currentError, self::STACKTRACE[1]);

                    $currentErrorStackTrace = substr($currentError, $stackTraceLocation, $second_occurence - 1);
                    $currentError = substr($currentError, 0, $stackTraceLocation - 1);

                    $currentErrorHour = substr($currentError, strpos($currentError, ":") - 2, 8);
                    $currentError = substr($currentError, strpos($currentError, $currentErrorHour) + 10);

                    $this->saveError($args = [
                        'error' => substr($currentError, 0, 50),
                        'stacktrace' => $currentErrorStackTrace,
                        'hour' => $currentErrorHour,
                        'date' => $searchDate,
                        'category' => $category,
                    ]);

                    $first_occurence = $second_occurence;
                    $second_occurence = strpos($file, $searchDate, $first_occurence + 10);
                }
            }
        }
    }

    private function saveError($args)
    {

        $existingError = AdvancedBugsterDB::where([
            ['date', '=', $args['date']],
            ['hour', '=', $args['hour']],
        ])->first();

        if ($existingError == null) {
            $newError = new AdvancedBugsterDB();

            $newError->full_url = 'parsed_log';
            $newError->path = 'log';
            $newError->file = $args['category'] . " logs";
            $newError->message = $args['error'];
            $newError->trace = $args['stacktrace'];
            $newError->app_name = config('env.APP_NAME');
            $newError->debug_mode = config('env.APP_DEBUG');

            $newError->date = $args['date'];
            $newError->hour = $args['hour'];

            $newError->save();
        }
    }

    private function iterateThroughDirectory($path)
    {
        $dir = scandir($path);

        foreach ($dir as $file) {
            if (strpos($file, '.log')) {
                $aux = $path;
                $array1 = explode('/', $aux);
                $array = array_pop($array1);
                $this->errorLogs[$array][] = ['path' => $path, 'name' => $file];
            } elseif (strpos($file, '.') >= 0 && strrpos($file, '.') !== false) {
                continue;
            } else {
                $this->iterateThroughDirectory($path . '/' . $file);
            }
        }
    }

    private function validateFile($path, $file)
    {
        return is_file($path . '/' . $file);
    }

    private function showFileErrors()
    {
        if (!empty($this->errors)) {
            foreach ($this->errors as $error) {
                $this->info($error);
            }
        }
    }

}
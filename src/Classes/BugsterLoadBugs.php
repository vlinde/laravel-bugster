<?php

namespace Vlinde\Bugster\Classes;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\URL;
use Vlinde\Bugster\Models\AdvancedBugsterDB;

class BugsterLoadBugs
{
    public function saveError($request, \Throwable $exception, $saveType = 'HTTP')
    {
        if ($saveType == 'HTTP') {
            $trace = $exception->getTrace();
            $trace = json_encode(array_slice($trace, 0, 5));

            $error = [
                'full_url' => $request->fullUrl(),
                'path' => $request->path(),
                'method' => $request->method(),
                'status_code' => $exception->getCode(),
                'line' => $exception->getLine(),
                'file' => $exception->getFile(),
                'message' => strlen($exception->getMessage()) ? $exception->getMessage() : "No message",
                'trace' => $trace,
                'user_id' => Auth::user() ? Auth::user()->id : 0,
                'previous_url' => URL::previous(2),
                'app_env' => config('app.env'),
                'app_name' => config('env.APP_NAME'),
                'debug_mode' => config('env.APP_DEBUG'),
                'ip_address' => $request->ip(),
                'headers' => json_encode($request->header())
            ];
            if (config('bugster.use_redis') == true) {
                $conn = Redis::connection('Bugster');
                $conn->set("error_log" . Carbon::now()->toDateString() . ":error_log" . str_replace(":", "-", Carbon::now()->toTimeString()), json_encode($error), 'EX', 172800);
            } else {
                $this->saveErrorToSql($error);
            }
        }

        if ( $saveType == 'TERMINAL' ) {
            $error = [
                'full_url' => 'TERMINAL',
                'path' => 'TERMINAL',
                'method' => 'TEMRINAL',
                'status_code' => $exception->getCode(),
                'line' => $exception->getLine(),
                'file' => $exception->getFile(),
                'message' => strlen($exception->getMessage()) ? $exception->getMessage() : "No message",
                'trace' => '',
                'user_id' => Auth::user() ? Auth::user()->id : 0,
                'previous_url' => 'TERMINAL',
                'app_env' => config('app.env'),
                'app_name' => config('env.APP_NAME'),
                'debug_mode' => config('env.APP_DEBUG'),
                'ip_address' => 'HOST',
                'headers' => 'TERMINAL'
            ];
            if (config('bugster.use_redis') == true) {
                $conn = Redis::connection('Bugster');
                $conn->set("error_log" . Carbon::now()->toDateString() . ":error_log" . str_replace(":", "-", Carbon::now()->toTimeString()), json_encode($error), 'EX', 172800);
            } else {
                $this->saveErrorToSql($error);
            }
        }
    }

    public function saveErrorToSql($error) {
        $bugsterBug = new AdvancedBugsterDB();

        $bugsterBug->full_url = $error['full_url'];
        $bugsterBug->path = $error['path'];
        $bugsterBug->method = $error['method'];
        $bugsterBug->status_code = $error['status_code'];
        $bugsterBug->line = $error['line'];
        $bugsterBug->file = $error['file'];
        $bugsterBug->message = $error['message'];
        $bugsterBug->trace = $error['trace'];
        $bugsterBug->user_id = $error['user_id'];
        $bugsterBug->previous_url = $error['previous_url'];
        $bugsterBug->app_name = $error['app_env'];
        $bugsterBug->debug_mode = $error['debug_mode'];
        $bugsterBug->ip_address = $error['ip_address'];
        $bugsterBug->headers = '';

        $bugsterBug->save();
    }
}

<?php

namespace Vlinde\Bugster\Classes;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Vlinde\Bugster\Models\AdvancedBugsterDB;

class BugsterLoadBugs
{
    public function saveError($request, Throwable $exception, $saveType = 'HTTP'): void
    {
        $trace = $exception->getTraceAsString();

        if ($exception instanceof HttpExceptionInterface) {
            $code = $exception->getStatusCode();
        } else {
            $code = $exception->getCode();
        }

        $message = 'No message';

        if ($exception->getMessage() !== '') {
            $message = $exception->getMessage();
        } elseif ($code === 404) {
            $message = "$code - " . $request->path();
        }

        $type = null;

        if ($code === 404 || $code === 500) {
            $type = 'error';
        }

        $dateTime = now()->toDateTimeString();

        [$date, $hour] = explode(' ', $dateTime);
        $date = trim($date);
        $hour = trim($hour);

        if ($saveType === 'HTTP') {
            $log = [
                'type' => $type,
                'full_url' => $request->fullUrl(),
                'path' => $request->path(),
                'method' => $request->method(),
                'status_code' => $code,
                'line' => $exception->getLine(),
                'file' => Str::after($exception->getFile(), $request->getHost()),
                'message' => $message,
                'trace' => $trace,
                'user_id' => Auth::check() ? Auth::id() : 0,
                'previous_url' => URL::previous(2),
                'app_env' => config('app.env'),
                'app_name' => config('env.APP_NAME'),
                'debug_mode' => config('env.APP_DEBUG'),
                'ip_address' => $request->ip(),
                'headers' => json_encode($request->header()),
                'date' => $date,
                'hour' => $hour
            ];

            if (config('bugster.use_redis') === true) {
                $this->saveLogInRedis($log);
            } else {
                $this->saveLogInDB($log);
            }
        }

        if ($saveType === 'TERMINAL') {
            $log = [
                'type' => $type,
                'full_url' => 'TERMINAL',
                'path' => 'TERMINAL',
                'method' => 'TEMRINAL',
                'status_code' => $code,
                'line' => $exception->getLine(),
                'file' => $exception->getFile(),
                'message' => $message,
                'trace' => $trace,
                'user_id' => Auth::check() ? Auth::id() : 0,
                'previous_url' => 'TERMINAL',
                'app_env' => config('app.env'),
                'app_name' => config('env.APP_NAME'),
                'debug_mode' => config('env.APP_DEBUG'),
                'ip_address' => 'localhost',
                'headers' => 'TERMINAL',
                'date' => $date,
                'hour' => $hour
            ];

            if (config('bugster.use_redis') === true) {
                $this->saveLogInRedis($log);
            } else {
                $this->saveLogInDB($log);
            }
        }
    }

    public function saveLogInDB(array $log): void
    {
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

    public function saveLogInRedis(array $log): void
    {
        $conn = Redis::connection(config('bugster.redis_connection_name'));

        $now = str_replace([' ', ':', '-'], '', Carbon::now()->toDateTimeString());

        $conn->set("bugster:$now", json_encode($log), 'EX', 172800);
    }
}

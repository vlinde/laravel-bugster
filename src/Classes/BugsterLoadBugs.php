<?php

namespace Vlinde\Bugster\Classes;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;
use Vlinde\Bugster\Models\AdvancedBugsterDB;

class BugsterLoadBugs
{
    public function saveError($request, Throwable $exception, ?int $statusCode = null, $saveType = 'HTTP'): void
    {
        //        if ($exception instanceof HttpExceptionInterface) {
        //            $code = $exception->getStatusCode();
        //        } else {
        //            $code = $exception->getCode();
        //        }
        //
        //        $code = $code === 0 ? 500 : $code;

        if ($statusCode === null) {
            $statusCode = 500;
        }

        $message = 'No message';

        if ($exception->getMessage() !== '') {
            $message = $exception->getMessage();
        }

        $type = null;

        if ($statusCode === 404 || $statusCode === 500) {
            $type = 'error';
        }

        $dateTime = now()->toDateTimeString();

        [$date, $hour] = explode(' ', $dateTime);
        $date = trim($date);
        $hour = trim($hour);

        $context = [];
        if ($statusCode === 401) {
            $context = $request->all();
        }

        $log = [
            'type' => $type,
            'status_code' => $statusCode,
            'line' => $exception->getLine(),
            'message' => $message,
            'trace' => $exception->getTraceAsString(),
            'user_id' => Auth::check() ? Auth::id() : 0,
            'app_env' => config('app.env'),
            'app_name' => config('env.APP_NAME'),
            'debug_mode' => config('env.APP_DEBUG'),
            'date' => $date,
            'hour' => $hour,
            'context' => $context,
        ];

        if ($saveType === 'TERMINAL') {
            $log['full_url'] = 'TERMINAL';
            $log['path'] = 'TERMINAL';
            $log['method'] = 'TERMINAL';
            $log['file'] = $exception->getFile();
            $log['previous_url'] = 'TERMINAL';
            $log['ip_address'] = 'localhost';
            $log['headers'] = 'TERMINAL';
        } else {

            $referel = Request::server('HTTP_REFERER');

            if (empty($referel)) {
                $referel = 'direct';
            }

            $log['full_url'] = $request->fullUrl();
            $log['path'] = $request->path();
            $log['method'] = $request->method();
            $log['file'] = Str::after($exception->getFile(), $request->getHost());
            $log['previous_url'] = $referel;
            $log['ip_address'] = $request->ip();
            $log['headers'] = json_encode($request->header());
        }

        $logDriver = config('bugster.log_driver');

        switch ($logDriver) {
            case 'redis':
                $this->saveLogInRedis($log);
                break;
            case 'db':
                $this->saveLogInDB($log);
                break;
            case 'file':
                $this->saveLogInFile($log);
                break;
            default:
                Log::error("Saving log from Bugster failed. Invalid log driver '$logDriver'");
        }
    }

    public function saveLogInRedis(array $log): void
    {
        $redis = Redis::connection(config('bugster.redis_connection_name'));

        $now = str_replace([' ', ':', '-'], '', Carbon::now()->toDateTimeString());

        $redis->set("bugster:$now", json_encode($log), 'EX', 172800);
    }

    public function saveLogInDB(array $log): void
    {
        AdvancedBugsterDB::create([
            'full_url' => $log['full_url'],
            'category' => 'laravel',
            'type' => $log['type'],
            'path' => $log['path'],
            'method' => $log['method'],
            'status_code' => $log['status_code'],
            'line' => $log['line'],
            'file' => $log['file'],
            'message' => $log['message'],
            'trace' => null,
            'user_id' => $log['user_id'],
            'previous_url' => $log['previous_url'],
            'app_name' => $log['app_env'],
            'debug_mode' => $log['debug_mode'],
            'ip_address' => $log['ip_address'],
            'headers' => null,
            'date' => $log['date'],
            'hour' => $log['hour'],
        ]);
    }

    public function saveLogInFile(array $log): void
    {
        $fullMessage = "ip: {$log['ip_address']} | method: {$log['method']} | code: {$log['status_code']} | ref: {$log['previous_url']} | path: {$log['path']} | message: {$log['message']}";

        Log::channel(config('bugster.log_channel'))->info($fullMessage, $log['context'] ?? []);
    }
}

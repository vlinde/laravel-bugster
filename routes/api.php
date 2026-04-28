<?php

use Illuminate\Support\Facades\Route;
use Vlinde\Bugster\Http\Controllers\LogFileController;
use Vlinde\Bugster\Http\Controllers\StatusCodeController;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

Route::get('log-files', [LogFileController::class, 'index']);
Route::get('log-files/download', [LogFileController::class, 'download'])->name('log-files.download');
Route::post('log-files/rename', [LogFileController::class, 'rename'])->name('log-files.rename');
Route::get('status-codes/chart', [StatusCodeController::class, 'chart']);

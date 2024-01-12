<?php

use Illuminate\Support\Facades\Route;
use Vlinde\Bugster\Http\Controllers\LogFileController;
use Vlinde\Bugster\Http\Controllers\StatusCodeController;

Route::get('log-files', [LogFileController::class, 'index']);
Route::get('log-files/download', [LogFileController::class, 'download'])->name('log-files.download');
Route::post('log-files/rename', [LogFileController::class, 'rename'])->name('log-files.rename');
Route::get('status-codes/chart', [StatusCodeController::class, 'chart']);

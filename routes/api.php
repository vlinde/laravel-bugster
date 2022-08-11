<?php

use Illuminate\Support\Facades\Route;
use Vlinde\Bugster\Http\Controllers\LogFileController;

Route::get('log-files', [LogFileController::class, 'index']);
Route::get('log-files/download', [LogFileController::class, 'download'])->name('log-files.download');

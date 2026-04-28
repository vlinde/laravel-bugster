<?php

use Illuminate\Support\Facades\Route;
use Laravel\Nova\Http\Requests\NovaRequest;

Route::get('/log-files', function (NovaRequest $request) {
    return inertia('BugsterLogs');
});

Route::get('/status-codes-chart', function (NovaRequest $request) {
    return inertia('BugsterStatusCodesChart');
});

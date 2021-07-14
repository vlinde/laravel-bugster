<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::post("/bugster_generate_stats", function () {
    Artisan::call("bugster:generate:stats");

    return response()->json([
        "message_success" => "Stats generated successfully",
    ]);
});

Route::post("/bugster_parse_logs", function () {
    Artisan::call("bugster:generate:errors");

    return response()->json([
        "message_success" => "Errors generated successfully",
    ]);
});

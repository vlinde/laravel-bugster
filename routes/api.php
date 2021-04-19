<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::get('/endpoint', function (Request $request) {
//     //
// });

Route::post("/bugster_generate_stats", function (Request $request){
    \Illuminate\Support\Facades\Artisan::call("bugster:generate:stats");

    return response()->json([
        "message_success" => "Stats generated successfully",
    ]);
});

Route::post("/bugster_parse_logs", function (Request $request){
    \Illuminate\Support\Facades\Artisan::call("bugster:generate:errors");

    return response()->json([
        "message_success" => "Errors generated successfully",
    ]);
});

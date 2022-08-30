<?php

return [
    'log_driver' => 'redis', // available; redis, db, file (in log channel)

    'log_channel' => 'daily',

    'redis_connection_name' => 'default',

    'enable_custom_log_paths' => false, // check custom logs files

    // custom log paths
    'log_paths' => [
        'example' => [ //category
            'path' => storage_path('example'), // file path
            'file' => 'example.log', // file name
        ],
    ],

    // when to notify stats
    'microsoft_team_hook' => env('MS_TEAMS_WEBHOOK_URL')
];

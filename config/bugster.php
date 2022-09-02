<?php

return [
    // available; redis, db, file
    'log_driver' => 'redis',

    // Set log channel
    'log_channel' => 'daily',

    // Set the redis connection to save the log in Redis
    'redis_connection_name' => 'default',

    // check logs from custom logs
    'enable_custom_log_paths' => false,

    'log_paths' => [
        'example' => [
            'path' => storage_path('example'),
            'file' => 'example.log',
        ],
    ],

    // Redis connection to check if queues are working
    'redis_connection_for_queues_status' => 'default',

    // Microsoft Teams webhook for notification
    'microsoft_team_hook' => env('MS_TEAMS_WEBHOOK_URL')
];

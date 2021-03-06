<?php

return [
    'use_redis' => true,

    'redis_connection_name' => 'default',

    'enable_custom_log_paths' => false,

    'log_paths' => [
        'example' => [ //category
            'path' => storage_path('example'), //file path
            'file' => 'example.log', //file name
        ],
    ],

    'microsoft_team_hook' => env('MS_TEAMS_WEBHOOK_URL')
];

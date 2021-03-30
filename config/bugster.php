<?php

return [

    'redis_connection_name' => 'default',

    'log_paths' => [
        'laravel' => [
            'path' => storage_path('logs'),
            'file' => 'laravel.log',
        ],
        'ngnix' => [
            'path' => storage_path('logs/ngnix'),
            'file' => 'ngnix-error.log'
        ],
//        'custom' => 'etc'
    ],
];

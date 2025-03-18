<?php

return [
    'log_driver' => 'redis',

    'log_channel' => 'daily',

    'redis_connection_name' => 'default',

    'redis_connection_for_queues_status' => 'default',

    'microsoft_team_hook' => env('MS_TEAMS_WEBHOOK_URL'),

    'statistic_keys' => [],
];

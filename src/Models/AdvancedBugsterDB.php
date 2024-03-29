<?php

namespace Vlinde\Bugster\Models;

use Illuminate\Database\Eloquent\Model;

class AdvancedBugsterDB extends Model
{
    protected $table = 'laravel_bugster_bugs';

    protected $fillable = [
        'last_apparition',
        'category',
        'type',
        'full_url',
        'path',
        'method',
        'status_code',
        'line',
        'file',
        'message',
        'trace',
        'user_id',
        'previous_url',
        'app_name',
        'debug_mode',
        'ip_address',
        'headers',
        'date',
        'hour',
    ];
}

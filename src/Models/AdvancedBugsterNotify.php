<?php

namespace Vlinde\Bugster\Models;

use Illuminate\Database\Eloquent\Model;

class AdvancedBugsterNotify extends Model
{
    protected $table = 'laravel_bugster_notifications';

    protected $fillable = [
        'statistic_key',
        'min_value',
    ];
}

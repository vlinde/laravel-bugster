<?php

namespace Vlinde\Bugster\Models;

use Illuminate\Database\Eloquent\Model;

class LaravelBugsterWebhook extends Model
{
    protected $fillable = [
        'type', 'url', 'payload', 'active',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}

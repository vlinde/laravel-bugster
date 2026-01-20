<?php

namespace Vlinde\Bugster\Models;

use Illuminate\Database\Eloquent\Model;

class LaravelBugsterWebhook extends Model
{
    protected $fillable = [
        'url', 'payload', 'active',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}

<?php

namespace Vlinde\Bugster\Models;

use Illuminate\Database\Eloquent\Model;

class LaravelBugsterStatusCode extends Model
{
    protected $table = 'laravel_bugster_status_codes';

    protected $fillable = [
        'display_name', 'code', 'count', 'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function getStatusCodeTextAttribute(): string
    {
        return "$this->code ($this->display_name)";
    }
}

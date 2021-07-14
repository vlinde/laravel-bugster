<?php

namespace Vlinde\Bugster\Models;

use Illuminate\Database\Eloquent\Model;

class AdvancedBugsterStat extends Model
{
    protected $table = 'laravel_bugster_stats';

    protected $fillable = [
        'error',
        'generated_at',
        'category',
        'file',
        'daily',
        'weekly',
        'monthly'
    ];

    public function bugs()
    {
        return $this->belongsToMany(
            AdvancedBugsterDB::class,
            'bugster_bug_bugster_stat',
            'laravel_bugster_stat_id',
            'laravel_bugster_bug_id'
        );
    }
}

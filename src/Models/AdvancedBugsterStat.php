<?php

namespace Vlinde\Bugster\Models;

use Illuminate\Database\Eloquent\Model;

class AdvancedBugsterStat extends Model
{

    public function url() {
        return $this->belongsTo(AdvancedBugsterLink::class, 'url_id', 'id');
    }

    public function bugs() {
        return $this->belongsToMany(AdvancedBugsterDB::class, 'bugster_bug_bugster_stat', 'laravel_bugster_stat_id', 'laravel_bugster_bug_id');
    }

    protected $table = 'laravel_bugster_stats';
}

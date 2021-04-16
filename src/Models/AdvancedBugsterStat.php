<?php

namespace Vlinde\Bugster\Models;

use Illuminate\Database\Eloquent\Model;

class AdvancedBugsterStat extends Model
{

    public function url() {
        return $this->belongsTo(AdvancedBugsterLink::class, 'url_id', 'id');
    }

    public function links() {
        return $this->belongsToMany(AdvancedBugsterLink::class, 'bugster_link_bugster_stat', 'laravel_bugster_stat_id', 'laravel_bugster_link_id');
    }

    protected $table = 'laravel_bugster_stats';
}

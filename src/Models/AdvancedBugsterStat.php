<?php

namespace Vlinde\Bugster\Models;

use Illuminate\Database\Eloquent\Model;

class AdvancedBugsterStat extends Model
{

    public function url() {
        return $this->belongsTo(AdvancedBugsterLink::class, 'url_id', 'id');
    }

    protected $table = 'laravel_bugster_stats';
}

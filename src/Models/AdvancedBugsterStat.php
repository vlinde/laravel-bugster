<?php

namespace Vlinde\Bugster\Models;

use Illuminate\Database\Eloquent\Model;

class AdvancedBugsterStat extends Model
{

    public function url() {
        return $this->hasOne(AdvancedBugsterLink::class);
    }

    protected $table = 'laravel_bugster_stats';
}

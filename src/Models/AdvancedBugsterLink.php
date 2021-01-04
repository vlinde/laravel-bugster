<?php

namespace Vlinde\Bugster\Models;

use Illuminate\Database\Eloquent\Model;

class AdvancedBugsterLink extends Model
{

    public function stats() {
        return $this->hasMany(AdvancedBugsterStat::class);
    }

    public function errors() {
        return $this->hasMany(AdvancedBugsterDB::class);
    }

    protected $table = 'laravel_bugster_links';
}

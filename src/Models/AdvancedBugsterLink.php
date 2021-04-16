<?php

namespace Vlinde\Bugster\Models;

use Illuminate\Database\Eloquent\Model;

class AdvancedBugsterLink extends Model
{

    public function stats() {
        return $this->hasMany(AdvancedBugsterStat::class);
    }

    public function errors() {
        return $this->belongsToMany(AdvancedBugsterDB::class, "bugster_bug_bugster_link", "laravel_bugster_link_id", "laravel_bugster_bug_id" );
    }

    protected $table = 'laravel_bugster_links';
}

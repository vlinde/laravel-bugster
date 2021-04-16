<?php

namespace Vlinde\Bugster\Models;

use Illuminate\Database\Eloquent\Model;

class AdvancedBugsterDB extends Model
{

    public function links() {
        return $this->belongsToMany(AdvancedBugsterLink::class, "bugster_bug_bugster_link", "laravel_bugster_bug_id", "laravel_bugster_link_id" );
    }

    protected $table = 'laravel_bugster_bugs';
}

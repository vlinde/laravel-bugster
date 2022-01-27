<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBugsterBugBugsterStatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bugster_bug_bugster_stat', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('laravel_bugster_bug_id');
            $table->unsignedBigInteger('laravel_bugster_stat_id');
            $table->timestamps();

            $table->foreign("laravel_bugster_bug_id")->references("id")->on("laravel_bugster_bugs")->onDelete('cascade');
            $table->foreign("laravel_bugster_stat_id")->references("id")->on("laravel_bugster_stats")->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bugster_bug_bugster_stat');
    }
}

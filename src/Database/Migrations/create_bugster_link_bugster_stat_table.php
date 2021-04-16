<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBugsterLinkBugsterStatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bugster_link_bugster_stat', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('laravel_bugster_link_id');
            $table->unsignedBigInteger('laravel_bugster_stat_id');
            $table->timestamps();

            $table->foreign("laravel_bugster_link_id")->references("id")->on("laravel_bugster_links");
            $table->foreign("laravel_bugster_stat_id")->references("id")->on("laravel_bugster_stats");
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bugster_link_bugster_link');
    }
}

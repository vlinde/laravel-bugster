<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaravelBugsterStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laravel_bugster_stats', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedBigInteger('url_id')->nullable();
            $table->text('errors');
            $table->timestamps();

            $table->foreign('url_id')->references('id')->on('laravel_bugster_links');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('laravel_bugster_stats');
    }
}

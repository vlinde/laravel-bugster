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
            $table->string('error');
            $table->text('generated_at');
            $table->text('category');
            $table->text('file');
            $table->integer('daily')->nullable();
            $table->integer('weekly')->nullable();
            $table->integer('monthly')->nullable();
            $table->timestamps();
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

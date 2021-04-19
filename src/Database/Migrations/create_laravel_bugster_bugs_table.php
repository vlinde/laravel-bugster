<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaravelBugsterBugsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laravel_bugster_bugs', function (Blueprint $table) {
            $table->id();
            $table->text("last_apparition")->nullable();
            $table->string("category")->nullable();
            $table->string('full_url');
            $table->string('path')->nullable();
            $table->string('method')->nullable();
            $table->string('status_code')->nullable();
            $table->string('line')->nullable();
            $table->string('file')->nullable();
            $table->text('message')->nullable();
            $table->text('trace')->nullable();
            $table->string('user_id')->nullable();
            $table->string('previous_url')->nullable();
            $table->string('app_name')->nullable();
            $table->string('debug_mode')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('headers')->nullable();
            $table->string('date')->nullable();
            $table->string('hour')->nullable();
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
        Schema::dropIfExists('laravel_bugster_bugs');
    }
}

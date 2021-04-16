<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBugsterBugBugsterLinkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bugster_bug_bugster_link', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('laravel_bugster_bug_id');
            $table->unsignedBigInteger('laravel_bugster_link_id');
            $table->timestamps();

            $table->foreign("laravel_bugster_bug_id")->references("id")->on("laravel_bugster_bugs");
            $table->foreign("laravel_bugster_link_id")->references("id")->on("laravel_bugster_links");
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bugster_bug_bugster_link');
    }
}

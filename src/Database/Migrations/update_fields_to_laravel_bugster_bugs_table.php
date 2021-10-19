<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFieldsToLaravelBugsterBugsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('laravel_bugster_bugs', function (Blueprint $table) {
            $table->mediumText('message')->change();
            $table->longText('trace')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('laravel_bugster_bugs', function (Blueprint $table) {
            $table->text('message')->change();
            $table->text('trace')->change();
        });
    }
}

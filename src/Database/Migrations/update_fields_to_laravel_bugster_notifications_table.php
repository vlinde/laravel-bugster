<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFieldsToLaravelBugsterNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('laravel_bugster_notifications', function (Blueprint $table) {
            $table->bigInteger('min_value')->nullable()->change();
            $table->bigInteger('max_value')->nullable()->after('min_value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('laravel_bugster_notifications', function (Blueprint $table) {
            $table->bigInteger('min_value')->change();
            $table->dropColumn('max_value');
        });
    }
}

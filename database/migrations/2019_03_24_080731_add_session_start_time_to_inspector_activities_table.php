<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSessionStartTimeToInspectorActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inspector_activities', function (Blueprint $table) {
            $table->dateTime('session_start_time')->after('id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inspector_activities', function (Blueprint $table) {
            $table->dropColumn('session_start_time');
        });
    }
}

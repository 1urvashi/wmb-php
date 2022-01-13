<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTraderIdToTraderLogHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trader_log_histories', function (Blueprint $table) {
            $table->integer('trader_id')->nullable()->unsigned()->after('time');
            $table->foreign('trader_id')->references('id')->on('trader_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trader_log_histories', function (Blueprint $table) {
            $table->dropColumn(['trader_id']);
        });
    }
}

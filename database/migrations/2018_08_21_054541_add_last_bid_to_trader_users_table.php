<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLastBidToTraderUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trader_users', function (Blueprint $table) {
            $table->dateTime('last_bid')->nullable()->after('device_id_actual');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trader_users', function (Blueprint $table) {
            $table->dropColumn('last_bid');
        });
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdditionalDocColumsToTraderUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trader_users', function (Blueprint $table) {
            $table->text('kyc')->after('trade_license')->nullable();
            $table->text('payment_receipt')->after('kyc')->nullable();
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
            $table->dropColumn(['kyc','payment_receipt']);
        });
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOwnerNegotiateAcution extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('auctions', function (Blueprint $table) {
            $table->dateTime('negotiatedTime')->after('is_negotiated')->nullable();
            $table->double('final_req_amount', 12, 2)->after('negotiated_amount')->nullable();
            $table->dateTime('ownerNegotiatedTime')->after('negotiated_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('auctions', function (Blueprint $table) {
            $table->dropColumn('final_req_amount');
        });
    }
}

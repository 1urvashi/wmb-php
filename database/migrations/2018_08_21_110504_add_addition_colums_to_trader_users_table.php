<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdditionColumsToTraderUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trader_users', function (Blueprint $table) {
             $table->integer('country_id')->unsigned()->after('dmr_id')->nullable();
             $table->foreign('country_id')->references('id')->on('country')->onDelete('cascade');

             $table->string('post_code')->after('country_id')->nullable();
             $table->integer('emirate_id')->unsigned()->after('post_code')->nullable();
             $table->foreign('emirate_id')->references('id')->on('emirates')->onDelete('cascade');
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
            $table->dropColumn(['country_id', 'post_code', 'emirate_id']);
        });
    }
}

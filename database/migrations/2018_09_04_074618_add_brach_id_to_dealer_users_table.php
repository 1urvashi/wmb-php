<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBrachIdToDealerUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dealer_users', function (Blueprint $table) {
            $table->integer('branch_id')->nullable()->default(0)->after('license');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dealer_users', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });
    }
}

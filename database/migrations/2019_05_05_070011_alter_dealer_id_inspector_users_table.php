<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDealerIdInspectorUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inspector_users', function (Blueprint $table) {
            $table->unsignedInteger('dealer_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inspector_users', function (Blueprint $table) {
            $table->unsignedInteger('dealer_id')->nullable(false)->change();
        });
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTypeToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('type')->comment('1=> ADMIN, 2=> USERS, 3=> DRM, 4=> SUPER ADMIN, 5=> ONBOARDER, 6=> BRANCH MANAGER')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('type')->after('image')->comment('1=> ADMIN, 2=> USERS, 3=> DRM')->nullable();
        });
    }
}

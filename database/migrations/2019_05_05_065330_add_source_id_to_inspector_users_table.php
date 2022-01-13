<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSourceIdToInspectorUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inspector_users', function (Blueprint $table) {
            $table->char('source_id', 36)->after('dealer_id')->nullable();
            $table->foreign('source_id')->references('id')->on('inspector_sources')->onDelete('cascade');
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
            $table->dropColumn('source_id');
        });
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDevicesInspectorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inspector_users', function (Blueprint $table) {
             $table->string('device_type')->nullable();
             $table->text('device_id')->nullable();
             $table->text('device_id_actual')->nullable();
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
            $table->dropColumn(['device_type','device_id','device_id_actual']);
        });
    }
}

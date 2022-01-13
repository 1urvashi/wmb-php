<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumsToObjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('objects', function (Blueprint $table) {
            $table->string('variation')->after('images_uploaded')->nullable();
            $table->string('vin')->after('variation')->nullable();
            $table->string('vehicle_registration_number')->after('vin')->nullable();
            $table->integer('customer_id')->after('vehicle_registration_number')->unsigned()->nullable();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('objects', function (Blueprint $table) {
            $table->dropColumn(['variation', 'vin', 'vehicle_registration_number', 'customer_id']);
        });
    }
}

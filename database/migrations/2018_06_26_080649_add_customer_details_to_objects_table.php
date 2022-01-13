<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomerDetailsToObjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('objects', function (Blueprint $table) {
            $table->string('customer_name')->after('vehicle_registration_number')->nullable();
            $table->string('customer_mobile')->after('customer_name')->nullable();
            $table->string('customer_email')->after('customer_mobile')->nullable();
            $table->string('source_of_enquiry')->after('customer_email')->nullable();
            $table->integer('bank_id')->after('source_of_enquiry')->unsigned()->nullable();
            $table->foreign('bank_id')->references('id')->on('banks')->onDelete('cascade');
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
            $table->dropColumn(['customer_name', 'customer_mobile', 'customer_email', 'source_of_enquiry', 'bank_id']);
        });
    }
}

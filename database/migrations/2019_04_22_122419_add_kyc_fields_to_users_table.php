<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKycFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trader_users', function (Blueprint $table) {
            $table->integer('kycBusinessLowSize')->default(0)->after('last_bid');
            $table->integer('kycBusinessUpSize')->default(0)->after('last_bid');
            $table->bigInteger('kycCreditLimit')->default(0)->after('last_bid');
            $table->integer('kycCarAge')->default(0)->after('last_bid');

            $table->bigInteger('kycMileage')->default(0)->after('last_bid');

            $table->text('company_name')->nullable();
            $table->string('trade_license_no', 255)->nullable();
            $table->string('tax_registration_no', 255)->nullable();
            $table->string('emirates_id', 255)->nullable()->after('last_bid');
            $table->date('emiratesIdExpiry')->nullable()->after('last_bid');


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
              $table->dropColumn(['kycBusinessLowSize', 'kycBusinessUpSize', 'kycCreditLimit', 'kycCarAge', 'kycMileage']);
        });
    }
}

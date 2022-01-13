<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumsToSalesTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_types', function (Blueprint $table) {
            $table->tinyInteger('sale_type')->after('name')->comment('1=>Traditional, 2=>Hybrid')->nullable();
            $table->double('rta_charge', 15, 2)->default(0)->after('sale_type')->nullable();
            $table->double('poa_charge', 15, 2)->default(0)->after('rta_charge')->nullable();
            $table->double('transportation_charge', 15, 2)->default(0)->after('poa_charge')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_types', function (Blueprint $table) {
            $table->dropColumn(['sale_type', 'rta_charge', 'poa_charge', 'transportation_charge']);
        });
    }
}

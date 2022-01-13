<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomerAmountToInspectorNegaotiatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inspector_negaotiates', function (Blueprint $table) {
            $table->decimal('customer_amount', 8, 2)->default(0)->after('override_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inspector_negaotiates', function (Blueprint $table) {
            $table->dropColumn('customer_amount');
        });
    }
}

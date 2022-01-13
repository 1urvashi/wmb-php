<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumsToAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attributes', function (Blueprint $table) {
            $table->boolean('invisible_to_trader')->after('option')->default(1)->comment('0=>Visible, 1=>Invisible')->nullable();
            $table->boolean('exportable')->after('invisible_to_trader')->default(1)->comment('0=>false, 1=>true')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attributes', function (Blueprint $table) {
            $table->dropColumn(['invisible_to_trader', 'exportable']);
        });
    }
}

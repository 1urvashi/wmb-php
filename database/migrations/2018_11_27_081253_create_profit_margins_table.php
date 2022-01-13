<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfitMarginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profit_margins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->double('range_from', 15, 2)->nullable();
            $table->double('range_to', 15, 2)->nullable();
            $table->tinyInteger('profit_status')->default(1)->comment('1=>Fixed Profit, 2=>Percentage Profit')->nullable();
            $table->double('profit_amount', 15, 2)->nullable();
            $table->bigInteger('sales_type_id')->unsigned()->nullable();
            $table->foreign('sales_type_id')->references('id')->on('sales_types')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('profit_margins');
    }
}

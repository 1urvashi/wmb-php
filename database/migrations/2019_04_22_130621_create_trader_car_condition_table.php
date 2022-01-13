<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTraderCarConditionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trader_car_condition', function (Blueprint $table) {
            $table->uuid('id');
            $table->primary('id');
            $table->integer('carConditionId')->nullable()->unsigned();
            $table->foreign('carConditionId')->references('id')->on('car_condition');
            $table->integer('traderId')->nullable()->unsigned();
            $table->foreign('traderId')->references('id')->on('trader_users');
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
        Schema::drop('trader_car_condition');
    }
}

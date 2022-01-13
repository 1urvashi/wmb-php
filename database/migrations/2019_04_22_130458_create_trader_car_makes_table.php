<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTraderCarMakesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trader_car_makes', function (Blueprint $table) {
            $table->uuid('id');
            $table->primary('id');
            $table->integer('carMakeId')->nullable()->unsigned();
            $table->foreign('carMakeId')->references('id')->on('car_makes');
            $table->integer('traderId')->nullable()->unsigned();
            $table->foreign('traderId')->references('id')->on('trader_users');
            $table->string('otherTitle')->nullable();
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
        Schema::drop('trader_car_makes');
    }
}

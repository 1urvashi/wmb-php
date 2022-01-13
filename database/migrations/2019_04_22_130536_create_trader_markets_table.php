<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTraderMarketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trader_markets', function (Blueprint $table) {
          $table->uuid('id');
          $table->primary('id');
          $table->integer('marketId')->nullable()->unsigned();
          $table->foreign('marketId')->references('id')->on('markets');
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
        Schema::drop('trader_markets');
    }
}

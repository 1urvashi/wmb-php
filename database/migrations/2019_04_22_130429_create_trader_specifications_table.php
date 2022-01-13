<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTraderSpecificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trader_specifications', function (Blueprint $table) {
          $table->uuid('id');
          $table->primary('id');
          $table->integer('specificationId')->nullable()->unsigned();
          $table->foreign('specificationId')->references('id')->on('specifications');
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
        Schema::drop('trader_specifications');
    }
}

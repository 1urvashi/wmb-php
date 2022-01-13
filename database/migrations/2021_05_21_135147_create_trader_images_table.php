<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTraderImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trader_images', function (Blueprint $table) {
            $table->uuid('id');
            $table->primary('id');
            $table->integer('traderId')->nullable()->unsigned();
            $table->foreign('traderId')->references('id')->on('trader_users');
            $table->text('image');
            $table->string('imageType', 50);
            $table->integer('sort')->default(0);
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
        Schema::drop('trader_images');
    }
}

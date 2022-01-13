<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInspectorNegaotiatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inspector_negaotiates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('auction_id')->unsigned()->nullable();
            $table->foreign('auction_id')->references('id')->on('auctions')->onDelete('cascade');
            $table->integer('inspector_id')->unsigned()->nullable();
            $table->foreign('inspector_id')->references('id')->on('inspector_users')->onDelete('cascade');
            $table->decimal('override_amount', 8, 2)->nullable();
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
        Schema::drop('inspector_negaotiates');
    }
}

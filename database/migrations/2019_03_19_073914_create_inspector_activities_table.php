<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInspectorActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inspector_activities', function (Blueprint $table) {
            $table->uuid('id');
            $table->primary('id');
            $table->integer('inspector_id')->unsigned()->nullable();
            $table->foreign('inspector_id')->references('id')->on('inspector_users');

            $table->integer('object_id')->unsigned()->nullable();
            $table->foreign('object_id')->references('id')->on('objects');
            
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->string('type')->nullable();
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
        Schema::drop('inspector_activities');
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGenaralNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('genaral_notifications', function (Blueprint $table) {
            $table->uuid('id');
            $table->primary('id');
            $table->string('title')->nullable();
            $table->text('body')->nullable();
            $table->integer('user_count')->comment('0 for all users')->nullable();
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
        Schema::drop('genaral_notifications');
    }
}

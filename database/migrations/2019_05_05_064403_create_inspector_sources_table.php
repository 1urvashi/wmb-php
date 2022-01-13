<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInspectorSourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inspector_sources', function (Blueprint $table) {
            $table->uuid('id');
            $table->primary('id');
            $table->string('title')->nullable();
            $table->tinyInteger('status')->comment('1=> Enable, 0=> Disable')->default(1)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('inspector_sources');
    }
}

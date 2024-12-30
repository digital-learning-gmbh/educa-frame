<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRaumsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raums', function (Blueprint $table) {
            $table->increments('id');
            // Attribute
            $table->string('name');
            $table->string('gebaeude')->nullable();
            $table->integer('size');
            $table->string('bemerkungen')->nullable();
            $table->string('ausstattung')->nullable();
            $table->unsignedInteger("external_booking_id")->nullable();
            $table->timestamps();
        });

        Schema::create('raum_schule', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('schule_id');
            $table->foreign('schule_id')
                ->references('id')->on('schules');
            $table->unsignedInteger('raum_id');
            $table->foreign('raum_id')
                ->references('id')->on('raums');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('raum_schule');
        Schema::dropIfExists('raums');
    }
}

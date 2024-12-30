<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKlasses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('klasses', function (Blueprint $table) {
            $table->increments('id');
            // Belongs to schuljahr
            $table->unsignedInteger('schuljahr_id');
            $table->foreign('schuljahr_id')
                ->references('id')->on('schuljahrs');

            $table->date('von')->nullable();
            $table->date('bis')->nullable();
            $table->unsignedInteger('raum_id')->nullable();
            $table->foreign('raum_id')
                ->references('id')->on('raums');

            $table->integer('fs')->nullable();
            $table->string('name');

            $table->enum("type",["schoolclass","planning_group","special","blocking_group","cluster_group","free_group"])->default("planning_group");
            $table->enum("daysOfWork",["mo_tu","tu_we","we_th","th_fr","fr_sa","block"])->nullable();


            $table->unsignedInteger('lehrplan_einheit_id')->nullable();

            $table->string('beschreibung')->nullable();

            $table->unsignedInteger("external_booking_id")->nullable();
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
        Schema::dropIfExists('klasses');
    }
}

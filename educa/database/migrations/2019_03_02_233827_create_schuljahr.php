<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchuljahr extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schuljahrs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('year');
            $table->string('name');
            // Schuljahr Beginn und Ende wann unterrichtet wird
            $table->dateTime('start');
            $table->dateTime('ende');
            // just the complete time
            $table->dateTime('general_start')->nullable();
            $table->dateTime('general_ende')->nullable();

            $table->integer('period_length')->default(5);
            $table->integer('hours_day')->default(5);

            // belongs to school
            $table->unsignedInteger('schule_id');
            $table->foreign('schule_id')
                ->references('id')->on('schules');
            $table->boolean('planung')->default(true);
            // fertig, ausgewählter Entwurf
            $table->unsignedInteger('entwurf_id')->nullable();

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
        Schema::dropIfExists('schuljahrs');
    }
}

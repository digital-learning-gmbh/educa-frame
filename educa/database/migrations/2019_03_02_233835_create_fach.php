<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFach extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faches', function (Blueprint $table) {
            $table->increments('id');
            // Belongs to a schule
            $table->unsignedInteger('schule_id');
            $table->foreign('schule_id')
                ->references('id')->on('schules');
            // attributes
            $table->string('name');
            $table->string('duration')->default(2);
            $table->string('color')->default("#415FA8");
            $table->string('abk')->default("");
            $table->string('beschreibung')->default("");

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
        Schema::dropIfExists('faches');
    }
}

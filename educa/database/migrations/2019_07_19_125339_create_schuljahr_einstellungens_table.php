<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchuljahrEinstellungensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schuljahr_einstellungens', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('schuljahr_id');
            $table->foreign('schuljahr_id')
                ->references('id')->on('schuljahrs');
            $table->string("key");
            $table->string("value");
            
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
        Schema::dropIfExists('schuljahr_einstellungens');
    }
}

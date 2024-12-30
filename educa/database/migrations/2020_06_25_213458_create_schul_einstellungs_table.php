<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchulEinstellungsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schul_einstellungs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('schule_id');
            $table->foreign('schule_id')
                ->references('id')->on('schules');
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
        Schema::dropIfExists('schul_einstellungs');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormularAnhangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('formular_anhangs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('formular_abgeschickt_id');
            $table->foreign('formular_abgeschickt_id')
                ->references('id')->on('formular_abgeschickts');
            $table->string("key"); // ersteller, referenz, etc.
            $table->string('model_type')->nullable(); // user, schuler etc.
            $table->unsignedInteger('model_id')->nullable(); // id of the model
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
        Schema::dropIfExists('formular_anhangs');
    }
}

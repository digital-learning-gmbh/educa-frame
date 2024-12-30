<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchuljahrEntwurvesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schuljahr_entwurves', function (Blueprint $table) {
            $table->increments('id');
            // gehört zu einem Schuljahr
            $table->string("name")->default("Neuer Entwurf");
            $table->unsignedInteger('schuljahr_id');
            $table->foreign('schuljahr_id')
                ->references('id')->on('schuljahrs');

            $table->string("clingonotes")->nullable();
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
        Schema::dropIfExists('schuljahr_entwurves');
    }
}

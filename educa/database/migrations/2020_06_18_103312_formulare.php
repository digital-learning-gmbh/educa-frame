<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Formulare extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('formulars', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name');
            $table->unsignedInteger('schule_id')->nullable();
            $table->foreign('schule_id')
                ->references('id')->on('schules');

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('formular_revisions', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('number');

            // Änderung durch Nutzer
            $table->unsignedBigInteger('user_id');

            $table->unsignedInteger('formular_id');
            $table->foreign('formular_id')
                ->references('id')->on('formulars');

            // The data
            $table->longText('data')->nullable();

            $table->timestamps();
        });

        //Intermediate for referencing
        Schema::create('model_revision', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedInteger('model_id')->nullable(); // id of the model
            $table->string('model_type')->nullable(); // 'schuler', 'curriculum', 'klasse'

            $table->unsignedInteger('formular_revision_id')->nullable();
            $table->foreign('formular_revision_id')
                ->references('id')->on('formular_revisions');


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
        Schema::dropIfExists('model_revision');
        Schema::dropIfExists('formular_revisions');
        Schema::dropIfExists('formulars');
    }
}

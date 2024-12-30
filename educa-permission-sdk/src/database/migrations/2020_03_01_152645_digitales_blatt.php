<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DigitalesBlatt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('digitales_blatts', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name');
            // Erstellt durch Nutzer
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')
                ->references('id')->on('users');


            $table->enum('type', ['arbeitsblatt', 'evaluation', 'elternbrief'])->default('arbeitsblatt');

            // Share
            $table->string('zugriffscode')->nullable();
            $table->boolean('public')->default(false);

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('digitales_blatt_revisions', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('number');
            // Erstellt durch Nutzer
            $table->unsignedInteger('digitales_blatt_id');
            $table->foreign('digitales_blatt_id')
                ->references('id')->on('digitales_blatts');

            // The data
            $table->longText('data')->nullable();

            $table->timestamps();
        });

        Schema::create('blatt_executions', function (Blueprint $table) {
            $table->increments('id');

            // Ausgefüllt durch Nutzer
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')
                ->references('id')->on('users');

            // in der Revision
            $table->unsignedInteger('revisions_id');
            $table->foreign('revisions_id')
                ->references('id')->on('digitales_blatt_revisions');

            $table->longText('values')->nullable();
            $table->Integer('time')->nullable();
            $table->timestamps();
        });

        Schema::create('geteiltes_digitales_blatts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('digitales_blatt_id');
            $table->foreign('digitales_blatt_id')
                ->references('id')->on('digitales_blatts');
            $table->unsignedInteger('digitales_blatt_revision_id');
            $table->foreign('digitales_blatt_revision_id')
                ->references('id')->on('digitales_blatt_revisions');

            // Revision
            $table->unsignedInteger('school_class_id');

            // ab wann und bis wann
            $table->dateTime('verfuegbar_ab')->nullable();
            $table->dateTime('verfuegbar_bis')->nullable();
            $table->timestamps();
        });


        Schema::dropIfExists('blatt_executions');

        Schema::create('blatt_executions', function (Blueprint $table) {
            $table->increments('id');

            // Ausgefüllt durch Nutzer
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')
                ->references('id')->on('users');

            // in der Revision
            $table->unsignedInteger('geteiltes_blatt_id');
            $table->foreign('geteiltes_blatt_id')
                ->references('id')->on('geteiltes_digitales_blatts');

            $table->longText('values')->nullable();
            $table->Integer('time')->nullable();
            $table->timestamps();
        });

        Schema::create('db_cached_evaluations', function (Blueprint $table) {
            $table->increments('id');
            // bezieht sich immer auf ein geteiltes Blatt
            $table->unsignedInteger('geteiltes_digitales_blatt_id');
            $table->foreign('geteiltes_digitales_blatt_id')
                ->references('id')->on('geteiltes_digitales_blatts');

            $table->enum('state', ['wait', 'run', 'finish', 'error'])->default('wait');


            $table->longText('data')->nullable();

            // Auswertung durch einen Nutzer beauftragt
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')
                ->references('id')->on('users');

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
        Schema::dropIfExists('db_cached_evaluations');
        Schema::dropIfExists('blatt_executions');
        Schema::dropIfExists('geteiltes_digitales_blatts');
        Schema::dropIfExists('blatt_executions');
        Schema::dropIfExists('digitales_blatt_school_class');
        Schema::dropIfExists('digitales_blatt_revisions');
        Schema::dropIfExists('digitales_blatts');
    }
}

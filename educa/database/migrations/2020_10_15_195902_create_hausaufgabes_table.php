<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHausaufgabesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("title");
            $table->dateTime('start')->nullable();
            $table->dateTime('end')->nullable();
            $table->unsignedBigInteger('cloud_id');
            $table->foreign('cloud_id')
                ->references('id')->on('cloud_i_d_s');
            $table->string("handIn")->default("no"); // no, file, other ..
            $table->json("meta")->nullable();
            $table->longText("notes")->nullable();
            $table->longText("description")->nullable();
            $table->dateTime("planned_for")->nullable(); // if null, direkt

            $table->string("contentId")->nullable(); // Refactor later

            $table->integer("remember_minutes")->default("-1");
            $table->boolean("remember_sent")->default(false);
            $table->boolean("finishSetup")->default(false);
            $table->timestamps();
        });

        Schema::create('task_section', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('task_id');
            $table->foreign('task_id')
                ->references('id')->on('tasks');
            $table->unsignedBigInteger('section_id');
            $table->foreign('section_id')
                ->references('id')->on('sections');
            $table->timestamps();
        });

        Schema::create('task_cloud_i_d', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('task_id');
            $table->foreign('task_id')
                ->references('id')->on('tasks');
            $table->unsignedBigInteger('cloud_id');
            $table->foreign('cloud_id')
                ->references('id')->on('cloud_i_d_s');
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
        Schema::dropIfExists('task_cloud_i_d');
        Schema::dropIfExists('task_section');
        Schema::dropIfExists('tasks');
    }
}

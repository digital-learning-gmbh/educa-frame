<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormularAbgeschicktsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('formular_abgeschickts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('formular_revision_id')->nullable();
            $table->text('formular_data')->nullable();
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
        Schema::dropIfExists('formular_abgeschickts');
    }
}

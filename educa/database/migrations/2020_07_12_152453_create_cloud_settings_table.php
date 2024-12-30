<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCloudSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cloud_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('cloud_i_d_id');
            $table->foreign('id')
                ->references('id')->on('cloud_i_d_s');
            $table->string("app");
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
        Schema::dropIfExists('cloud_settings');
    }
}

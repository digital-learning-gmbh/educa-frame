<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSectionSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_section_settings', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('cloud_id')->nullable();
            $table->foreign('cloud_id')->references('id')->on('cloud_i_d_s');
            
            $table->unsignedBigInteger('section_id');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete("cascade");
            
            $table->boolean("notificationDisabled");
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
        Schema::dropIfExists('user_section_settings');
    }
}

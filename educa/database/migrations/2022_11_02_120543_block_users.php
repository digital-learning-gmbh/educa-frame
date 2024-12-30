<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BlockUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blocked_users', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('cloudid'); //blocked user
            $table->foreign('cloudid')
                ->references('id')->on('cloud_i_d_s');

            $table->unsignedBigInteger('by_cloudid'); //blocked from
            $table->foreign('by_cloudid')
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
        Schema::dropIfExists('blocked_users');
    }
}

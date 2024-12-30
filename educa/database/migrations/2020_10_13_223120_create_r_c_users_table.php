<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRCUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rc_users', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('cloudid'); //autor
            $table->foreign('cloudid')
                ->references('id')->on('cloud_i_d_s');

            $table->string("uid");
            $table->string("email");
            $table->string("password");
            $table->string("username");
            $table->string("access_token")->nullable();
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
        Schema::dropIfExists('rc_users');
    }
}

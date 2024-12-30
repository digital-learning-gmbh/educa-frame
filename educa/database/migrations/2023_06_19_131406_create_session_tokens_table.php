<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSessionTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('session_tokens', function (Blueprint $table) {
            $table->id();
            $table->string("token",1024)->unique();
            $table->unsignedBigInteger('cloudid');
            $table->foreign('cloudid')
                ->references('id')->on('cloud_i_d_s');
            $table->timestamp("last_seen")->nullable();
            $table->string("browser")->nullable();
            $table->string("app")->nullable();
            $table->string("device")->nullable();
            $table->string("os")->nullable();
            $table->boolean("isAdmin")->default(false);
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
        Schema::dropIfExists('session_tokens');
    }
}

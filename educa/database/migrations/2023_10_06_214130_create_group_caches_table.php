<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupCachesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_caches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cloudid');
            $table->foreign('cloudid')
                ->references('id')->on('cloud_i_d_s');
            $table->unsignedBigInteger('groupid')->index();
            $table->json("cache");
            $table->index(["cloudid","groupid"]);

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
        Schema::dropIfExists('group_caches');
    }
}

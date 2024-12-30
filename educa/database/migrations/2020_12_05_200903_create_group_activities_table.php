<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feed_activities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('belong_id');
            $table->enum('belong_type',['group', 'user']);
            $table->unsignedBigInteger("creator")->nullable();
            $table->string("creator_model")->nullable();
            $table->string("merge_id");
            $table->string("type");
            $table->json("payload")->nullable();
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
        Schema::dropIfExists('group_activities');
        Schema::dropIfExists('feed_activities');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeetingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('b_b_b_servers', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->boolean("active");
            $table->string("base_url");
            $table->string("secret");
            $table->unsignedBigInteger("load")->default(0);

            $table->timestamps();
        });

        Schema::create('model_meetings', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string("name");
            $table->string("type");
            $table->string("model_type");
            $table->string("model_id");
            $table->string("meeting_id");
            $table->string("password_moderator");
            $table->string("password_member");
            $table->unsignedBigInteger("bbb_server");
            $table->foreign("bbb_server")->references("id")->on("b_b_b_servers");
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
        Schema::dropIfExists('model_meetings');
        Schema::dropIfExists('b_b_b_servers');
    }
}

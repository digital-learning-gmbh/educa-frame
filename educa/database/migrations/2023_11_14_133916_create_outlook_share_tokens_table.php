<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutlookShareTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outlook_share_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cloud_id');
            $table->foreign('cloud_id')->references('id')->on('cloud_i_d_s');
            $table->string("token");
            $table->json("filters")->nullable();
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
        Schema::dropIfExists('outlook_share_tokens');
    }
}

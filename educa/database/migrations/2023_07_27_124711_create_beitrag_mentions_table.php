<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeitragMentionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beitrag_mentions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('beitrag_id')->nullable(); // nullable
            $table->foreign('beitrag_id')
                ->references('id')->on('beitrags');

            $table->unsignedBigInteger('cloud_id');
            $table->foreign('cloud_id')
                ->references('id')->on('cloud_i_d_s');
            $table->string('mention_idx');
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
        Schema::dropIfExists('beitrag_mentions');
    }
}

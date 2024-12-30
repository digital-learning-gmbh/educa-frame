<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreatorProgressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('creator_progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cloudid')->nullable();
            $table->foreign('cloudid')
                ->references('id')->on('cloud_i_d_s');
            $table->string("job_id");
            $table->enum("variant",["transform","creation"])->default("transform");
            $table->enum("step",["started","closed"])->default("started");
            $table->json("detail_progress")->nullable(); // ai progress
            $table->text("learnContentText")->nullable();
            $table->json("targetLearnContents")->nullable(); // { "subTargetCount": 4, "target" : "no_complex", subTargets: ["crosswords","",""] }
            $table->json("learnContents")->nullable(); // [{ "order", "learnContentId"  }]
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
        Schema::dropIfExists('creator_progress');
    }
}

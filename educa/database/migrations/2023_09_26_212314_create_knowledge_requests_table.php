<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKnowledgeRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('knowledge_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cloudid')->nullable();
            $table->foreign('cloudid')
                ->references('id')->on('cloud_i_d_s');
            $table->string("job_id");
            $table->enum("step",["started","closed"])->default("started");
            $table->json("detail_progress")->nullable();
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
        Schema::dropIfExists('knowledge_requests');
    }
}

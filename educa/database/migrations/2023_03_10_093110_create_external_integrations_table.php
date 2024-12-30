<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExternalIntegrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('external_integrations', function (Blueprint $table) {
            $table->id();

            $table->enum("type",["link"])->default("link");
            $table->string("icon");
            $table->string("displayName");
            $table->longText("description")->nullable();
            $table->string("url");
            $table->json("params")->nullable();
            $table->unsignedBigInteger("template_id")->nullable(); // no foreign key

            $table->unsignedBigInteger('group_id');
            $table->foreign('group_id')
                ->references('id')->on('groups');

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
        Schema::dropIfExists('external_integrations');
    }
}

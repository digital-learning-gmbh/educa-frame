<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExternalIntegrationTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('external_integration_templates', function (Blueprint $table) {
            $table->id();
            $table->enum("type",["link"])->default("link");
            $table->string("icon")->default("");
            $table->string("displayName");
            $table->longText("description")->nullable();
            $table->string("url");
            $table->json("params")->nullable();
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
        Schema::dropIfExists('external_integration_templates');
    }
}

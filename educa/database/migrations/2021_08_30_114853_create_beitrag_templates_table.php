<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeitragTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beitrag_templates', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('cloudid'); //autor
            $table->foreign('cloudid')
                ->references('id')->on('cloud_i_d_s');

            $table->unsignedBigInteger('app_id');
            $table->foreign('app_id')
                ->references('id')->on('section_group_apps');

            $table->string("title");

            $table->longText('content');

            $table->timestamps();
        });

        Schema::create('beitrag_template_media', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('beitrag_template_id');
            $table->foreign('beitrag_template_id')
                ->references('id')->on('beitrag_templates');

            $table->string("disk_name"); //Dateiname
            $table->string("content_type")->default("image");
            $table->json("metadata")->nullable();

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
        Schema::dropIfExists('beitrag_template_media');
        Schema::dropIfExists('beitrag_templates');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGruppeTables extends Migration
{
    /**
     * Erstellt alle nötigen Tabellen für das Gruppen-Feature
     *
     * @return void
     */
    public function up()
    {
        //Gruppen
        Schema::create('groups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("name");
            $table->string("color")->default("#3490DC");
            $table->string("image")->nullable();
            $table->string("external_identifier")->nullable()->index();
            $table->boolean("archived")->default(false);
            $table->enum("type",["closed","open","restricted","open_restricted"])->default("closed");
            $table->string("tenant_id")->nullable();
            $table->string("default_role_id")->nullable();
            $table->longText("description")->nullable();
            $table->timestamps();
        });

        // Gruppen Apps
        //max für maximalanzahl, -1 unbegrenzt, ansonsten absolut
        Schema::create('group_apps', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string("name");
            $table->string("type");
            $table->string("icon");
            $table->integer('max')->default(-1);

            $table->timestamps();
        });

        Schema::create('sections', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("name");
            $table->string("image")->nullable();
            $table->longText("description")->nullable();

            $table->integer("order")->default(0);

            $table->unsignedBigInteger('group_id');
            $table->foreign('group_id')
                ->references('id')->on('groups');

            $table->timestamps();
        });

        //Beziehung Gruppe-Reiter
        Schema::create('section_group_apps', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('group_app_id');
            $table->foreign('group_app_id')
                ->references('id')->on('group_apps');

            $table->unsignedBigInteger('section_id');
            $table->foreign('section_id')
                ->references('id')->on('sections');

            $table->string("name");
            $table->boolean('can_delete')->default(true);
            $table->json("parameters");

            $table->timestamps();
        });

        //Beziehung CloudID-Gruppe
        Schema::create('cloudid_group', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('cloudid');
            $table->foreign('cloudid')
                ->references('id')->on('cloud_i_d_s');

            $table->unsignedBigInteger('group_id');
            $table->foreign('group_id')
                ->references('id')->on('groups');

            $table->timestamps();
        });

        //Beiträge aus dem Beitrags-Reiter
        Schema::create('beitrags', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('cloudid'); //autor
            $table->foreign('cloudid')
                ->references('id')->on('cloud_i_d_s');

            $table->unsignedBigInteger('app_id');
            $table->foreign('app_id')
                ->references('id')->on('section_group_apps');

            $table->longText('content');

            $table->dateTime("planned_for")->nullable(); // if null, direkt
            $table->boolean("comments_active")->default(true);
            $table->boolean("comments_hide")->default(false);
            $table->boolean("notify")->default(false);
            $table->boolean("notify_sent")->default(false);

            $table->timestamps();
        });

        //Kommentare für Beiträge aus dem Beitrags-Reiter
        Schema::create('beitrag_comments', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('cloudid'); //autor
            $table->foreign('cloudid')
                ->references('id')->on('cloud_i_d_s');

            $table->unsignedBigInteger('beitrag_id');
            $table->foreign('beitrag_id')
                ->references('id')->on('beitrags');

            $table->longText("content");
            $table->boolean("hidden")->default(false);
            $table->boolean("edited")->default(false);

            $table->timestamps();
        });

        //Medien für Beiträge aus dem Beitrags-Reiter
        Schema::create('beitrag_media', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('beitrag_id');
            $table->foreign('beitrag_id')
                ->references('id')->on('beitrags');

            $table->string("disk_name"); //Dateiname
            $table->string("content_type")->default("image");
            $table->json("metadata")->nullable();

            $table->timestamps();
        });

        //Likes für Beiträge aus dem Beitrags-Reiter
        Schema::create('beitrag_likes', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('cloudid');
            $table->foreign('cloudid')
                ->references('id')->on('cloud_i_d_s');

            $table->unsignedBigInteger('beitrag_id');
            $table->foreign('beitrag_id')
                ->references('id')->on('beitrags');


            $table->timestamps();
        });

        //Tracke, wer Beiträge aus dem Beitrags-Reiter gesehen hat
        Schema::create('beitrag_read', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('cloudid');
            $table->foreign('cloudid')
                ->references('id')->on('cloud_i_d_s');

            $table->unsignedBigInteger('beitrag_id');
            $table->foreign('beitrag_id')
                ->references('id')->on('beitrags');

            $table->timestamps();
        });

        //Rollen in Gruppen
        Schema::create('gruppe_rolles', function (Blueprint $table) {
            $table->unsignedBigInteger('limit');

            $table->String('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gruppe_rolles');
        Schema::dropIfExists('beitrag_read');
        Schema::dropIfExists('beitrag_likes');
        Schema::dropIfExists('beitrag_media');
        Schema::dropIfExists('beitrag_comments');
        Schema::dropIfExists('beitrags');
        Schema::dropIfExists('cloudid_group');
        Schema::dropIfExists('section_group_apps');
        Schema::dropIfExists('sections');
        Schema::dropIfExists('group_apps');
        Schema::dropIfExists('groups');
    }
}

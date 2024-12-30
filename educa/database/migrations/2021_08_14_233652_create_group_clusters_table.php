<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupClustersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_clusters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("name");
            // erstellt von einem Nutzer
            $table->unsignedBigInteger('cloudid');
            $table->foreign('cloudid')
                ->references('id')->on('cloud_i_d_s');

            $table->integer("sort_priority")->default(0);
            $table->boolean("readonly")->default(false);
            $table->boolean("always_visible")->default(false);
            $table->boolean("collapsed")->default(false);

            $table->timestamps();
        });

        Schema::create('group_group_cluster', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('group_cluster_id');
            $table->foreign('group_cluster_id')
                ->references('id')->on('group_clusters');

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
        Schema::dropIfExists('group_group_cluster');
        Schema::dropIfExists('group_clusters');
    }
}

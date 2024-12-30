<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCloudIDSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cloud_i_d_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email')->unique();
            $table->string('objectguid')->nullable();
            $table->string('name');
            $table->string('language')->default("de");
            $table->string('password')->nullable();
            $table->string("loginServer");
            $table->string("loginType");
            $table->string("image")->nullable();
            $table->boolean('agreedPrivacy')->default(false);
            $table->string("google2fa_secret")->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('model_cloud_id', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("cloud_i_d_id");
            $table->string("appName");
            $table->string("model");
            $table->string("loginId");
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
        Schema::dropIfExists('model_cloud_id');
        Schema::dropIfExists('cloud_i_d_s');
    }
}

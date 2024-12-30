<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schules', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name');
            $table->string('abk')->nullable();
            $table->string('licence');
            $table->text('description')->nullable();
            $table->enum('type',['kindergarten','elementarySchool','secondarySchool','nursingSchool','university','company'])->default('secondarySchool');
            $table->string('accentColor')->default("#f8f9fa");
            $table->boolean('valid')->default(false);
            $table->unsignedInteger('info_id')->nullable();
            $table->timestamps();
        });

        Schema::create('schule_user', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('schule_id');
            $table->foreign('schule_id')
                ->references('id')->on('schules');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')
                ->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schule_user');
        Schema::dropIfExists('schules');
    }
}

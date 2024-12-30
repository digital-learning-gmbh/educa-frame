<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLehrersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lehrers', function (Blueprint $table) {
            $table->increments('id');
            // attributes
            $table->string('firstname');
            $table->string('lastname');
            $table->string('abk')->default("");
            // if they can use the klassenbuch
            $table->boolean("allowedToLogin")->default(false);
            $table->string('email')->default(""); // aus add info ggf.?
            $table->string('password')->default("");
            $table->integer('week_hours')->nullable();
            $table->double('faktor_default')->nullable();

            $table->string("securityToken")->default("");
            $table->rememberToken();

            $table->unsignedInteger('info_id')->nullable();
            $table->foreign('info_id')
                ->references('id')->on('additional_infos');

            $table->unsignedInteger("external_booking_id")->nullable();

            $table->enum('status', ['locked','active', 'inactive'])->default('active');
            $table->unsignedInteger("schule_id")->nullable();
            $table->timestamps();
        });

        // Lehrer unterricht n Fächer
        Schema::create('lehrer_fach', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('fach_id');
            $table->foreign('fach_id')
                ->references('id')->on('faches');
            $table->unsignedInteger('lehrer_id');
            $table->foreign('lehrer_id')
                ->references('id')->on('lehrers');
        });

        Schema::create('lehrer_schule', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('schule_id');
            $table->foreign('schule_id')
                ->references('id')->on('schules');
            $table->unsignedInteger('lehrer_id');
            $table->foreign('lehrer_id')
                ->references('id')->on('lehrers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lehrer_schule');
        Schema::dropIfExists('lehrer_fach');
        Schema::dropIfExists('lehrers');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UsersRefactoring extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {

            $table->unsignedInteger('info_id')->nullable();
            $table->foreign('info_id')
                ->references('id')->on('additional_infos');
        });

        Schema::table('additional_infos', function (Blueprint $table) {
            $table->string('department')->nullable();
            $table->string('firstname2')->nullable();
            $table->string('lastname_new')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function(Blueprint $table) {
            $table->dropForeign(['info_id']);
            $table->dropColumn('info_id');
        });

    }
}

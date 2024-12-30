<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchulersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schulers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('firstname');
            $table->string('firstname2')->nullable();
            $table->string('lastname');
            // Additional attributes
            $table->unsignedInteger('info_id')->nullable();
            $table->foreign('info_id')
                ->references('id')->on('additional_infos');

            $table->string('image')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('klasse_schuler', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('klasse_id');
            $table->foreign('klasse_id')
                ->references('id')->on('klasses')->onDelete('cascade');
            $table->unsignedInteger('schuler_id');
            $table->foreign('schuler_id')
                ->references('id')->on('schulers');
            $table->date("from")->nullable()->default(null);
            $table->date("until")->nullable()->default(null);
            $table->text("note")->nullable();
            $table->timestamps();
        });

        Schema::create('schuler_schule', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('schule_id');
            $table->foreign('schule_id')
                ->references('id')->on('schules');
            $table->unsignedInteger('schuler_id');
            $table->foreign('schuler_id')
                ->references('id')->on('schulers');
        });

        try {
            DB::statement("ALTER TABLE stupla_schulers ADD FULLTEXT( firstname, lastname)");
        } catch (\Exception $exception)
        {
            // schnauze
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schuler_schule');
        Schema::dropIfExists('klasse_schuler');
        Schema::dropIfExists('schulers');
    }
}

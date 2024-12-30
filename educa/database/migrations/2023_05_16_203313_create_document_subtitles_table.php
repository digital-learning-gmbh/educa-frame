<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentSubtitlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_subtitles', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("dokument_id");
            $table->foreign('dokument_id')
                ->references('id')->on('dokuments');
            $table->string("language");
            $table->longText("subtitle");
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
        Schema::dropIfExists('document_subtitles');
    }
}

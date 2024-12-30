<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dokument_parts', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('dokument_id')->nullable();
            $table->foreign('dokument_id')
                ->references('id')->on('dokuments')->onDelete("cascade");
            $table->string("vectorId")->nullable()->index();
            $table->longText("content")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokument_parts');
    }
};

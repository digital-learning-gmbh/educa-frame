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
        Schema::create('vector_queries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("cloud_id");
            $table->string("query");
            $table->string("index_name");
            $table->json("result")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vector_queries');
    }
};

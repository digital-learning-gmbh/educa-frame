<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLastSeenSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('last_seen_sections', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('section_id'); // nullable
            $table->foreign('section_id')
                ->references('id')->on('sections');

            $table->unsignedBigInteger('cloud_id'); // nullable
            $table->foreign('cloud_id')
                    ->references('id')->on('cloud_i_d_s');

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
        Schema::dropIfExists('last_seen_sections');
    }
}

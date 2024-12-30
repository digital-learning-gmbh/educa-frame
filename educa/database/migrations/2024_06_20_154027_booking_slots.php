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
        Schema::create('booking_slots', function (Blueprint $table) {
            $table->id();

            $table->string("day_week");
            $table->time("start");
            $table->time("end");
            $table->integer("slot_duration");
            $table->integer("slot_breaks");

            $table->unsignedBigInteger('cloudid')->nullable(); // nullable
            $table->foreign('cloudid')
                ->references('id')->on('cloud_i_d_s');

            $table->timestamps();
        });

        Schema::create('booking_slot_blockeds', function (Blueprint $table) {
            $table->id();

            $table->dateTime("start");
            $table->dateTime("end");

            $table->unsignedBigInteger('cloudid')->nullable(); // nullable
            $table->foreign('cloudid')
                ->references('id')->on('cloud_i_d_s');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_slot_blockeds');
        Schema::dropIfExists('booking_slots');
    }
};

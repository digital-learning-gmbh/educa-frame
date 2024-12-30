<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountRecoveryOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_recovery_options', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('cloud_id')->nullable(); // nullable
            $table->foreign('cloud_id')
                ->references('id')->on('cloud_i_d_s');

            $table->boolean("emailRecover")->default(true);

            $table->boolean("questionRecover")->default(false);
            $table->text("firstQuestion")->default(null);
            $table->text("firstAnswer")->default(null);
            $table->text("secondQuestion")->default(null);
            $table->text("secondAnswer")->default(null);

            $table->boolean("secondEmailRecover")->default(false);
            $table->text("secondEmail")->default(null);
            
            $table->text("tempCode")->default(null);
            $table->dateTime("tempCodeUntil")->default(null);

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
        Schema::dropIfExists('account_recovery_options');
    }
}

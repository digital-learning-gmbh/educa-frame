<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateXApiStatementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('x_api_statements', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->longText("actor")->nullable();
            $table->string("actor_id")->nullable();
            
            $table->string("verb_short")->nullable();
            $table->longText("verb")->nullable();

            $table->string("object_type")->nullable();
            $table->string("object_id")->nullable();
            $table->longText("object")->nullable();

            $table->longText("context")->nullable();
            $table->longText("result")->nullable();

            $table->boolean("proxied")->default(false);

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
        Schema::dropIfExists('x_api_statements');
    }
}

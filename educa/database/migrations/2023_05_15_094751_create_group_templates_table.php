<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id'); // locked for tenant
            $table->foreign('tenant_id')
                ->references('id')->on('tenants');

            $table->string("name");
            $table->json("roles");
            $table->json("topics");
            $table->string("color")->default("#3490dc");
            $table->string("image")->nullable();
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
        Schema::dropIfExists('group_templates');
    }
}

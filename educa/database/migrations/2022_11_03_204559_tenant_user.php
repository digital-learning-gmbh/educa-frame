<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TenantUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tenant_cloudid', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('cloudid'); //blocked user
            $table->foreign('cloudid')
                ->references('id')->on('cloud_i_d_s');

            $table->unsignedBigInteger('tenant_id'); // locked for tenant
            $table->foreign('tenant_id')
                ->references('id')->on('tenants');

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
        Schema::dropIfExists('tenant_cloudid');
    }
}

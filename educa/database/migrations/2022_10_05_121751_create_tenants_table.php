<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("logo")->nullable();
            $table->boolean("hideLogoText")->default(true);
            $table->string("coverImage")->nullable();
            $table->string("color")->default("#202A44");
            $table->string("domain");
            $table->enum("dashboardLevel",["group","section"])->default("section");
            $table->string("licence");
            $table->string("chat")->nullable();
            $table->longText("impressum")->nullable();
            $table->integer("maxUsers")->default(-1);
            $table->boolean("overrideLoadingAnimation")->default(true);
            $table->boolean("isVisibleForOther")->default(true);
            $table->longText("information_text")->nullable();

            $table->longText("ms_graph_client_id")->nullable();
            $table->longText("ms_graph_secret_id")->nullable();
            $table->longText("ms_graph_tenant_id")->nullable();

            $table->boolean("allowRegister")->default(false);
            $table->bigInteger("roleRegister")->unsigned()->nullable();
            $table->boolean("allowPasswordReset")->default(false);
            $table->boolean("isFallBackTenant")->default(false);

            $table->longText("openai_key")->nullable();

            $table->longText("learnai_key")->nullable();

            $table->longText("keycloak_display")->nullable();
            $table->longText("keycloak_server")->nullable();
            $table->longText("keycloak_client_id")->nullable();
            $table->longText("keycloak_secret_id")->nullable();
            $table->longText("keycloak_realm")->nullable();

            $table->integer("token_used")->default(0);

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
        Schema::dropIfExists('tenants');
    }
}

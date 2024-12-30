<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $tenant = new Tenant();
        $tenant->name = "educa";
        $tenant->color = "#202A44";
        $tenant->logo = "neural.svg";
        $tenant->domain = "localhost";
        $tenant->licence = "default";
        $tenant->coverImage = "welcome.jpg";
        $tenant->overrideLoadingAnimation = false;
        $tenant->hideLogoText = false;
        $tenant->allowRegister = true;
        $tenant->isFallBackTenant = true;
        $tenant->openai_key = "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";

        $tenant->keycloak_display = "KeyCloak";
        $tenant->keycloak_server = "https://auth.digitallearning.gmbh";
        $tenant->keycloak_client_id = "educa_weiterbildung_odic";
        $tenant->keycloak_secret_id = "fpVi2iCF02vcGyB2I2qVaEX6hZ0RXs6w";
        $tenant->keycloak_realm = "master";

        $tenant->ms_graph_client_id = "682db7da-04b1-4877-91df-1f53a57ef00e";
        $tenant->ms_graph_secret_id = "Kvp8Q~kV9eCjUf7Z2zVWt1i4cmvGZGUyrP82mdyi";
        $tenant->ms_graph_tenant_id = "58bc0ba5-a0ca-49bd-9cef-6df03bc65248";

        $tenant->roleRegister = 6;

        $tenant->save();

        PermissionSeeder::createRolesForTenantSchool($tenant->id);

    }
}

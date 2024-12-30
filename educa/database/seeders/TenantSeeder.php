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
        $tenant->openai_key = "";

        $tenant->roleRegister = 6;

        $tenant->save();

        PermissionSeeder::createRolesForTenantSchool($tenant->id);

    }
}

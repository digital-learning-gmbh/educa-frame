<?php

namespace App\Http\Controllers\API\V1\Cloud;

use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\PermissionConstants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use StuPla\CloudSDK\Permission\Models\Role;
use StuPla\CloudSDK\Permission\Scope;

class TenantsController extends ApiController
{
    public function getAvailableTenants()
    {
        $user = parent::user();
        if($user == null)
            return parent::createJsonResponse("permission denied",true, 401);

        if($user->hasPermissionTo(PermissionConstants::IS_MULTI_TENANT_USER))
        {
            return parent::createJsonResponse("tenants",false, 200, ["tenants" => Tenant::all()]);
        }

        $tenants = $user->tenants;
        $tenants->each->makeVisible(['ms_graph_secret_id','keycloak_secret_id']);

        return parent::createJsonResponse("tenants",false, 200, ["tenants" => $user->tenants ]);
    }

    private function editTenant(Tenant $tenant, Request $request)
    {
        $tenant->name = $request->input("name");
        $tenant->licence = $request->input("licence");
        $tenant->color = $request->input("color");
        $tenant->overrideLoadingAnimation = !!$request->input("overrideLoadingAnimation");
        $tenant->hideLogoText = !!$request->input("hideLogoText");
        $tenant->allowRegister = !!$request->input("allowRegister");
        $tenant->allowPasswordReset = !!$request->input("allowPasswordReset");
        $tenant->roleRegister = $request->input("roleRegister");
        $tenant->maxUsers = $request->input("maxUsers");
        $tenant->impressum = $request->input("impressum");
        $tenant->information_text = $request->input("information_text");
        $tenant->isVisibleForOther = !!$request->input("isVisibleForOther");

        $tenant->ms_graph_client_id = $request->input("ms_graph_client_id");
        $tenant->ms_graph_secret_id = $request->input("ms_graph_secret_id");
        $tenant->ms_graph_tenant_id = $request->input("ms_graph_tenant_id");

        $tenant->keycloak_display = $request->input("keycloak_display");
        $tenant->keycloak_server = $request->input("keycloak_server");
        $tenant->keycloak_client_id = $request->input("keycloak_client_id");
        $tenant->keycloak_secret_id = $request->input("keycloak_secret_id");
        $tenant->keycloak_realm = $request->input("keycloak_realm");

        $tenant->openai_key = $request->input("openai_key");


        if(!$request->deleteCover && $request->newCover != null) {
            if($tenant->coverImage)
                $tenant->deleteCover();

            $cover = Str::random(32). "." . $request->newCover->extension();
            $coverPath = $request->newCover->storeAs('images/tenants', $cover, 'public');
            $tenant->coverImage = $cover;
        }

        if(!$request->deleteLogo && $request->newLogo != null) {
            if($tenant->logo)
                $tenant->deleteLogo();

            $logo = Str::random(32).".".$request->newLogo->extension();
            $logoPath = $request->newLogo->storeAs('images/tenants',$logo,'public');
            $tenant->logo = $logo;
        }

        if($request->deleteCover && $tenant->coverImage)
          $tenant->deleteCover();

        if($request->deleteLogo && $tenant->logo)
           $tenant->deleteLogo();

        return $tenant;
    }
    public function createTenant(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null || !$cloud_user->hasPermissionTo(PermissionConstants::SYSTEM_SETTINGS_CLOUD_TENANTS))
            die("Keine Rechte");
        $tenant = new Tenant();
        $tenant->domain = trim(strtolower($request->input("domain")));
        $this->editTenant($tenant, $request);
        $tenant->save();

        return parent::createJsonResponse("tenant created", false, 200, ["tenant" => $tenant]);

    }

    public function updateTenant($tenant_id, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null || !$cloud_user->hasPermissionTo(PermissionConstants::SYSTEM_SETTINGS_CLOUD_TENANTS))
            die("Keine Rechte");

        $tenant = Tenant::findOrFail($tenant_id);
        $this->editTenant($tenant, $request);
        $tenant->save();

        return parent::createJsonResponse("tenant edited", false, 200, ["tenant" => $tenant]);

    }

    public function deleteTenant($tenant_id, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null || !$cloud_user->hasPermissionTo(PermissionConstants::SYSTEM_SETTINGS_CLOUD_TENANTS))
            die("Keine Rechte");

        $tenant = Tenant::findOrFail($tenant_id);
        $tenant->delete();
        return parent::createJsonResponse("tenant deleted", false, 200,);

    }

    public function getTenant($tenant_id, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($cloud_user == null || !$cloud_user->hasPermissionTo(PermissionConstants::SYSTEM_SETTINGS_CLOUD_TENANTS))
            die("Keine Rechte");

        $tenant = Tenant::findOrFail($tenant_id);

        $roles = [];
        foreach (Role::where('guard_name','=', 'cloud')->where('scope_name','=', Scope::getDefaultName())->orderBy("name")->get() as $role)
        {
            if(!$role->hasPermissionTo(\App\PermissionConstants::IS_MULTI_TENANT_USER) ||
                $cloud_user->hasPermissionTo(\App\PermissionConstants::IS_MULTI_TENANT_USER))
            {
                $roles[] = $role;
            }
        }
        return parent::createJsonResponse("tenant details", false, 200,["roles" => $roles]);
    }

}

<?php

namespace App\Http\Controllers\API\V1\Cloud;

use App\CloudID;
use App\Http\Controllers\API\ApiController;
use App\Imports\CloudIdImport;
use App\Models\SessionToken;
use App\Models\Tenant;
use App\PermissionConstants;
use App\Providers\AppServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use StuPla\CloudSDK\Permission\Models\Role;
use StuPla\CloudSDK\Permission\Scope;

class UserController extends ApiController
{

    public function userInfo($cloud_id, Request $request)
    {
        $user = parent::getUserForToken($request);

        $targetTenant = AppServiceProvider::getTenant();
        if(!$targetTenant && !$targetTenant->isAllowed($user,PermissionConstants::SYSTEM_SETTINGS_CLOUD_USER))
            return parent::createJsonResponse("no permission",true, 403);

        $cloudId = CloudID::findOrFail($cloud_id);
        $cloudId->load(["tenants"]);

        foreach ($cloudId->tenants as $tenant)
        {
            $tenant->isEditBlocked = true;
            if($tenant->isAllowed($user,PermissionConstants::SYSTEM_SETTINGS_CLOUD_USER))
            {
                $tenant->isEditBlocked = false;
                $tenant->possibleRoles = $tenant->roles($user);
                $tenant->hasRoles = $cloudId->rolesForTenant($tenant->id);
            }
        }

        $sessions = SessionToken::where("cloudid","=",$cloudId->id)->get();
        $cloudId->isSuperAdmin = $cloudId->hasPermissionTo(PermissionConstants::IS_MULTI_TENANT_USER);

        return parent::createJsonResponse("details",false, 200, ["cloudId" => $cloudId, "sessions" => $sessions, "superAdmin" => $user->hasPermissionTo(PermissionConstants::IS_MULTI_TENANT_USER)]);
    }

    public function userList(Request $request)
    {
        $user = parent::getUserForToken($request);
        $targetTenant = AppServiceProvider::getTenant();
        if($user == null)
            return parent::createJsonResponse("This token is not valid.", true, 400);

        if($user->hasPermissionTo(PermissionConstants::IS_MULTI_TENANT_USER)) {
            $cloudUsers = CloudID::with('rolesGlobal')->with('groups')->get();
        } else {
            $tenant_ids = $user->tenants()->pluck("tenants.id");
            $cloudUsers = CloudID::whereIn("cloud_i_d_s.id",DB::table("tenant_cloudid")->whereIn("tenant_id",$tenant_ids)->pluck("cloudid"))->get();
        }
        foreach ($cloudUsers as $cloudUser)
        {
              $cloudUser->apps = [];
        }
        if($cloudUser->count() < 200) {
            foreach ($cloudUsers as $cloudUser) {
                $cloudUser->apps = $cloudUser->getApps();
            }
        }

        $roles = [];
        foreach ($targetTenant->roles($user) as $role)
        {
            if(!$role->hasPermissionTo(\App\PermissionConstants::IS_MULTI_TENANT_USER) ||
                $user->hasPermissionTo(\App\PermissionConstants::IS_MULTI_TENANT_USER))
            {
                $roles[] = $role;
            }
        }


        return $this->createJsonResponse( "ok", false, 200, [ "users" => $cloudUsers, "roles" => $roles]);
    }

    public function saveUserRoles($cloud_id, Request $request)
    {
        $user = parent::getUserForToken($request);
        $cloudUser = CloudID::findOrFail($cloud_id);
        $targetTenantId = $request->input("tenant_id");
        $roleIds = $request->input("role_ids", []);

        $targetTenant = Tenant::findOrFail($targetTenantId);
        if(!$targetTenant && !$targetTenant->isAllowed($user,PermissionConstants::SYSTEM_SETTINGS_CLOUD_USER))
            return parent::createJsonResponse("no permission",true, 403);

        foreach ($targetTenant->roles() as $rolle)
        {
            $cloudUser->removeRole($rolle);
        }
        foreach ($roleIds as $roleId)
        {
            $role = Role::findById($roleId,'cloud', Scope::getDefaultName(), $targetTenantId);
            $cloudUser->assignRole($role);
        }

        return $this->createJsonResponse( "ok", false, 200);
    }

    public function changeMultipleUserRoles(Request $request)
    {
        $user = parent::getUserForToken($request);
        $targetTenantId = $request->input("tenant_id");
        $targetTenant = Tenant::findOrFail($targetTenantId);
        $add = $request->input("add");

        if (!$targetTenant && !$targetTenant->isAllowed($user, PermissionConstants::SYSTEM_SETTINGS_CLOUD_USER))
            return parent::createJsonResponse("no permission", true, 403);

        $cloudUserIds = $request->input("user_ids");
        $roleIds = $request->input("role_ids", []);

        foreach ($cloudUserIds as $cloudUserId){
            $cloudUser = CloudID::findOrFail($cloudUserId);
            foreach ($roleIds as $roleId) {
                $role = Role::findById($roleId, 'cloud', Scope::getDefaultName(), $targetTenantId);
                if ($add) {
                    $cloudUser->assignRole($role);
                }
                else {
                    $cloudUser->removeRole($role);
                }
            }
        }

        return $this->createJsonResponse("changed multiple user roles", false, 200);
    }

    public function excelImport(Request $request)
    {
        // TODO permissions

        if(!$request->hasFile("file"))
            return parent::createJsonResponse("no file", true, 400);

        $file = $request->file("file");

        try {
            DB::beginTransaction();
                Excel::import(new CloudIdImport, $file);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return parent::createJsonResponse("error", true, 400);
        }

        return parent::createJsonResponse("imported", false, 200);

    }

    public function updateUser($cloud_id, Request $request)
    {
        $cloudId = CloudID::findOrFail($cloud_id);
        $cloudId->agreedPrivacy = $request->input("agreedPrivacy");
        //$cloudId->email = $request->input("email");
        $cloudId->name = $request->input("name");
        if(strlen($request->input("newPassword")) > 1)
            $cloudId->password = bcrypt($request->input("newPassword"));
        $cloudId->save();
        $cloudId->tenants()->sync($request->input("tenantIds"));

        $cloudId->load(["rolesGlobal", "tenants"]);


        $cloudId->apps = $cloudId->getApps();
        $cloudId->load("groups");


        if($request->input("isSuperAdmin",false))
        {
            $cloudId->assignRole("Super-Administrator");
        } else {
            $cloudId->removeRole("Super-Administrator");
        }

        return $this->userInfo($cloud_id, $request);

    }
    public function createUser(Request $request)
    {
        $cloudId = new CloudID();

        $cloudId->agreedPrivacy = $request->input("agreedPrivacy");
        $cloudId->email = $request->input("email");
        $cloudId->name = $request->input("name");
        $cloudId->password = bcrypt($request->input("newPassword"));
        $cloudId->loginServer = 'local';
        $cloudId->loginType = 'eloquent';
        $cloudId->save();
        $cloudId->syncRolesGlobal($request->input("roleIds"));
        $cloudId->tenants()->sync($request->input("tenantIds"));

        $cloudId->load(["rolesGlobal", "tenants"]);

        return parent::createJsonResponse("updated", false, 200, ["cloudId" => $cloudId]);
    }

    public function deleteUser($cloud_id, Request $request){
        CloudID::findOrFail($cloud_id)->delete();
        return parent::createJsonResponse("deleted", false, 200);
    }
}


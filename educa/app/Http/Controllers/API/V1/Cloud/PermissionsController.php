<?php

namespace App\Http\Controllers\API\V1\Cloud;

use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\PermissionConstants;
use App\Providers\AppServiceProvider;
use Illuminate\Http\Request;
use StuPla\CloudSDK\Permission\Models\Permission;
use StuPla\CloudSDK\Permission\Models\Role;
use StuPla\CloudSDK\Permission\Scope;

class PermissionsController extends ApiController
{
    public function getAvailableRoles(Request $request)
    {
        $user = self::getUserForToken($request);
        $targetTenant = Tenant::findOrFail($request->tenant_id);
        if(!$targetTenant && !$targetTenant->isAllowed($user,PermissionConstants::SYSTEM_SETTINGS_CLOUD_RIGHTS))
            return parent::createJsonResponse("no permission",true, 403);

        $roles = $targetTenant->roles($user);
        return parent::createJsonResponse("roles",false, 200, ["roles" => $roles]);
    }

    public function getAvailablePermissions(Request $request)
    {
        $user = self::getUserForToken($request);
        if(!$user->hasPermissionTo(PermissionConstants::IS_MULTI_TENANT_USER))
        {
            $permissions = Permission::where('guard_name', '=', 'cloud')->where("name","NOT LIKE",PermissionConstants::IS_MULTI_TENANT_USER)->where('scope_name', '=', Scope::getDefaultName())->orderBy("name")->get();
        } else {
            $permissions = Permission::where('guard_name', '=', 'cloud')->where('scope_name', '=', Scope::getDefaultName())->orderBy("name")->get();
        }
        return parent::createJsonResponse("roles",false, 200, ["permissions" => $permissions]);
    }

    public function flipRolePermission(Request $request)
    {
        $user = self::getUserForToken($request);
        $role = Role::findOrFail($request->role_id);
        $permission = Permission::findOrFail($request->permission_id);

        $targetTenant = Tenant::findOrFail($request->tenant_id);
        if(!$targetTenant && !$targetTenant->isAllowed($user,PermissionConstants::SYSTEM_SETTINGS_CLOUD_RIGHTS))
            return parent::createJsonResponse("no permission",true, 403);

        if($role->hasPermissionTo($permission))
            $role->revokePermissionTo($permission);
        else
            $role->givePermissionTo($permission);

        return $this->getAvailableRoles($request);
    }

    public function createRole(Request $request)
    {
        $user = self::getUserForToken($request);
        $targetTenant = Tenant::findOrFail($request->tenant_id);
        if(!$targetTenant && !$targetTenant->isAllowed($user,PermissionConstants::SYSTEM_SETTINGS_CLOUD_RIGHTS))
            return parent::createJsonResponse("no permission",true, 403);

        $role = new Role();
        $role->guard_name = "cloud";
        $role->name = $request->input("name");
        $role->scope_id = $targetTenant->id;
        $role->scope_name = Scope::getDefaultName();
        $role->save();

        return parent::createJsonResponse("role created",false, 200);

    }

    public function editRole($role_id, Request $request)
    {
        $user = self::getUserForToken($request);
        $targetTenant = Tenant::findOrFail($request->tenant_id);
        if(!$targetTenant && !$targetTenant->isAllowed($user,PermissionConstants::SYSTEM_SETTINGS_CLOUD_RIGHTS))
            return parent::createJsonResponse("no permission",true, 403);

        $role = Role::findOrFail($role_id);
        $role->name = $request->input("name");
        $role->save();

        return parent::createJsonResponse("role created",false, 200);

    }

    public function deleteRole($role_id, Request $request)
    {
        $user = self::getUserForToken($request);
        $targetTenant = Tenant::findOrFail($request->tenant_id);
        if(!$targetTenant && !$targetTenant->isAllowed($user,PermissionConstants::SYSTEM_SETTINGS_CLOUD_RIGHTS))
            return parent::createJsonResponse("no permission",true, 403);


        $role = Role::findOrFail($role_id);
        $role->delete();

        return parent::createJsonResponse("role deleted",false, 200);

    }

}

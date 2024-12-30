<?php

namespace App\Models;

use App\CloudID;
use App\PermissionConstants;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use StuPla\CloudSDK\Permission\Models\Role;
use StuPla\CloudSDK\Permission\Scope;

class Tenant extends Model
{
    use HasFactory;

    protected $hidden = ["ms_graph_secret_id","keycloak_secret_id"];

    public function user()
    {
        return $this->belongsToMany(CloudID::class,"tenant_cloudid","tenant_id","cloudid");
    }
    public function deleteCover(){
        Storage::disk('public')->delete("/images/tenants/".$this->coverImage);
        $this->coverImage = null;
        $this->save();
    }

    public function deleteLogo(){
        Storage::disk('public')->delete("/images/tenants/".$this->logo);
        $this->logo = null;
        $this->save();
    }

    public function delete()
    {
        $this->deleteCover();
        $this->deleteLogo();
        return parent::delete();
    }

    public function isAllowed($cloudUser, $permission_name)
    {
        if($cloudUser->hasPermissionTo(PermissionConstants::IS_MULTI_TENANT_USER))// super-user
        {
            $permissions = $cloudUser->getAllPermissions();
        } else {
            $permissions = $cloudUser->getAllPermissions(Scope::getDefaultName(), $this->id);
        }
        foreach ($permissions as $permission)
        {
            if($permission->name = $permission_name)
                return true;
        }

        return false;
    }

    public function roles($user = null)
    {
        if($user == null)
            return Role::where('guard_name','cloud')->where( 'scope_name', Scope::getDefaultName())->where('scope_id', $this->id)->get();

        if($user->hasPermissionTo(PermissionConstants::IS_MULTI_TENANT_USER))
        {

            return Role::where('guard_name', '=', 'cloud')->where(function ($query) {
                $query->where('scope_id', "=", $this->id);//->orWhere("scope_id","=",Scope::getDefaultId())
            })
                ->with("permissions")->where('scope_name', '=', Scope::getDefaultName())->get();
        } else {
            return Role::where('guard_name', '=', 'cloud')->where('scope_id', "=", $this->id)->with("permissions")->where('scope_name', '=', Scope::getDefaultName())->get();
        }
    }
}

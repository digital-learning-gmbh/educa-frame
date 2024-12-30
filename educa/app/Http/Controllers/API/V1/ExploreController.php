<?php

namespace App\Http\Controllers\API\V1;

use App\Group;
use App\Http\Controllers\API\ApiController;
use App\Models\Tenant;
use Illuminate\Http\Request;

class ExploreController extends ApiController
{
    public function getTenants(Request $request)
    {
        return $this->createJsonResponse("get tenants", false, 200, ["tenants" => Tenant::all()]);
    }

    public function getPublicTenantGroups($tenant_id, Request $request)
    {
        $tenant = Tenant::where("id","=",$tenant_id)->first();
        if($tenant == null)
        {
            return $this->createJsonResponse("no tenant", true, 404);
        }
        $groups = Group::where("tenant_id","=",$tenant->id)->where("type","=","open")->get();
        $groups->each->append("membersCount");
        return $this->createJsonResponse("get tenants", false, 200, ["groups" => $groups]);
    }
}

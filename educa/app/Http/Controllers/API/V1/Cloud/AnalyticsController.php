<?php

namespace App\Http\Controllers\API\V1\Cloud;

use App\Http\Controllers\Analytics\ReportController;
use App\Http\Controllers\API\ApiController;
use App\PermissionConstants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use StuPla\CloudSDK\Permission\Models\Role;
use StuPla\CloudSDK\Permission\Scope;

class AnalyticsController extends ApiController
{
    public function getReports(Request $request)
    {
        $user = parent::user();
        if(!$user->hasPermissionTo(PermissionConstants::SYSTEM_SETTINGS_CLOUD_ANALYTICS))
        {
            return parent::createJsonResponse("roles and reports, no permission",true,401);
        }

        $reportController = new ReportController();
        $reports = collect();
        foreach ($reportController->loadAvaiableReports() as $report)
        {
            $obj = json_decode(json_encode($report));
            $obj->roles = Role::whereIn("id", DB::table("report_role")->where("report_id", "=", $report->id)->get()->pluck("role_id"))->get();
            $reports->push($obj);
        }

        $rols = Role::where('scope_name','=', Scope::getDefaultName())->get();

        return parent::createJsonResponse("roles and reports",false,200, ["roles" => $rols, "reports" => $reports]);
    }

    public function saveReportsRoles(Request $request)
    {
        $user = parent::user();
        if(!$user->hasPermissionTo(PermissionConstants::SYSTEM_SETTINGS_CLOUD_ANALYTICS))
        {
            return parent::createJsonResponse("roles and reports, no permission",true,401);
        }

        DB::table('report_role')->delete();
        $configuration_data = $request->input("report_roles");

        // should be [{report_id: , role_ids: [] }, {}]

        foreach ($configuration_data as $single_configuration)
        {
            $report_id = $single_configuration["report_id"];
            $rols = $single_configuration["role_ids"];
            if($rols == null || !is_array($rols))
                continue;
            foreach ($rols as $rol) {
                DB::table("report_role")->insert([
                    "role_id" => $rol,
                    "report_id" => $report_id
                ]);
            }
        }

        return $this->getReports($request);
    }
}

<?php

namespace App\Http\Controllers\API\V1\Administration\Masterdata;

use App\AdditionalInfo;
use App\Http\Controllers\API\V1\Administration\AdministationApiController;
use App\IBAEmployeeExtension;
use App\Schule;
use App\Schuler;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class MasterdataEmployeeController extends AdministationApiController
{
    /**
     * @OA\Get(
     *     tags={"masterdata", "v1"},
     *     path="/api/v1/administration/masterdata/schools/{school_id}/employee",
     *     description="",
     *     @OA\Parameter(
     *     name="school_id",
     *     required=true,
     *     in="path",
     *     description="id of the school",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Array of all employees of a school in the system with reduced additional information (masterdata)")
     * )
     */
    public function employees($school_id, Request $request)
    {
        // $school = Schule::findOrFail($school_id);
        $employees = User::all();
        foreach($employees as $e)
        {
            $addInfo = $e->getAddInfo();
            $e->load("schulen");
            foreach($addInfo->toArray() as $key=>$value)
            {
                if( $key !== "id")
                    $e->$key = $value;
            }

            $settings = IBAEmployeeExtension::where('user_id','=', $e->id)->first();
            if($settings != null)
            {
                $settings->departments = json_decode($settings->departments);
                $settings->function = json_decode($settings->function);
                $settings->sector = json_decode($settings->sector);
                $settings->study_ids = $e->studium->pluck("id");
                $settings->heritage_school = Schule::find($settings->heritage_schule_id);
                $settings->heritage_school_id = $settings->heritage_schule_id;
                $settings->studies = $e->studium;
            }
            $e->settings = $settings;
        }

        return parent::createJsonResponse("user of the system",false, 200, [ "employees" =>
            $employees ]);
    }

    public function employeeDetailed($empolyee_id, Request $request)
    {
        $employee = User::findOrFail($empolyee_id);
        $addInfo = $employee->getAddInfo();
        $employee->load("schulen");
        foreach($addInfo->toArray() as $key=>$value)
        {
            if( $key !== "id")
                $employee->$key = $value;
        }
        $settings = IBAEmployeeExtension::where('user_id','=', $employee->id)->first();
        if($settings != null)
        {
            $settings->departments = json_decode($settings->departments);
            $settings->function = json_decode($settings->function);
            $settings->sector = json_decode($settings->sector);
            $settings->study_ids = $employee->studium->pluck("id");
            $settings->heritage_school = Schule::find($settings->heritage_schule_id);
            $settings->heritage_school_id = $settings->heritage_schule_id;
            $settings->studies = $employee->studium;
        }

        return parent::createJsonResponse("user of the system",false, 200, [ "employee" =>
            $employee, "settings" =>  $settings ]);
    }


    /**
     * @OA\Post(
     *     tags={"masterdata", "v1"},
     *     path="/api/v1/administration/masterdata/employee/add",
     *     description="",
     *     @OA\Response(response="200", description="Add a employee with additional info")
     * )
     */
    public function createEmployee(Request $request)
    {
        // currently not the case, hovwer
        $school_id = $request->school_ids; // <<-- Array
        $employee = new User;
        $addInfo = new AdditionalInfo;
        foreach($request->object as $key=>$value)
        {
            if($key != "id" && $key != "info_id" && $key != "personalnummer")
            {
                if(Schema::hasColumn($employee->getTable(), $key))
                {
                    $employee->$key = $value;
                }
                elseif(Schema::hasColumn($addInfo->getTable(), $key))
                {
                    $addInfo->$key = $value;
                }
            }
        }

        $addInfo->save();
        $employee->info_id = $addInfo->id;
        $employee->save();

        if(!$request->object["school_ids"])
            return $this->createJsonResponse("No school Ids set", true, 400);
        $employee->schulen()->sync($request->object["school_ids"]);


        if($request->has("settings") && $employee->id != null)
        {
            $settings = IBAEmployeeExtension::where('user_id','=', $employee->id)->first();
            if($settings == null)
                $settings = new IBAEmployeeExtension;
            $settings->user_id = $employee->id;
            $settings->heritage_schule_id = $request->settings["heritage_school_id"] ?? null;
            $settings->function = json_encode($request->settings["function"] ?? null );
            $settings->departments = json_encode($request->settings["departments"] ?? null );
            $settings->sector = json_encode($request->settings["sector"] ?? null );

            $settings->save();
            $employee->studium()->sync($request->settings["study_ids"] ?? null);
        }

        return $this->employeeDetailed($employee->id, $request);
    }

    /**
     * @OA\Post(
     *     tags={"masterdata", "v1"},
     *     path="/api/v1/administration/masterdata/employee/{empolyee_id}/update",
     *     description="",
     *     @OA\Parameter(
     *     name="empolyee_id",
     *     required=true,
     *     in="path",
     *     description="id of the employee",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Update an employee with additional info")
     * )
     */
    public function updateEmpolyee($empolyee_id, Request $request)
    {
        $employee = User::findOrFail($empolyee_id);
        $addInfo = $employee->getAddInfo();
        foreach($request->object as $key=>$value)
        {
            if($key != "id" && $key != "info_id" && $key != "personalnummer")
            {
                if($key == "birthdate")
                    $value = $value == null? null : Carbon::createFromTimestamp($value)->toDateTime();

                if(Schema::hasColumn($employee->getTable(), $key) && $key != "email" )
                {
                    $employee->$key = $value;
                }
                elseif(Schema::hasColumn($addInfo->getTable(), $key))
                {
                    $addInfo->$key = $value;
                }
            }
        }
        $addInfo->save();
        $employee->save();
        if(!$request->object["school_ids"])
            return $this->createJsonResponse("No school Ids set", true, 400);
        $employee->schulen()->sync($request->object["school_ids"]);


        if($request->has("settings") && $request->settings != null && $employee->id != null)
        {
            $settings = IBAEmployeeExtension::where('user_id','=', $employee->id)->first();
            if($settings == null)
                $settings = new IBAEmployeeExtension;
            $settings->user_id = $employee->id;
            $settings->heritage_schule_id = $request->settings["heritage_school_id"] ?? null;
            $settings->function = json_encode($request->settings["function"] ?? null );
            $settings->departments = json_encode($request->settings["departments"] ?? null );
            $settings->sector = json_encode($request->settings["sector"] ?? null );

            $settings->save();
            $employee->studium()->sync($request->settings["study_ids"] ?? null);
        }


        return $this->employeeDetailed($employee->id, $request);
    }

    public function deleteEmployee($employee_id, Request $request)
    {
        $employee = User::findOrFail($employee_id);
        return parent::createJsonResponse("contact deleted.", false, 200, $employee->delete());
    }
}

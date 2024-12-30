<?php

namespace App\Http\Controllers\API\V1\Administration\Curricula;

use App\Http\Controllers\API\V1\Administration\AdministationApiController;
use App\Klasse;
use App\Kohorte;
use App\Lehrplan;
use App\Schule;
use App\Schuljahr;
use App\Studium;
use Illuminate\Http\Request;

class CohortController extends AdministationApiController
{
    /**
     * @OA\Get(
     *     tags={"masterdata", "v1"},
     *     path="/api/v1/administration/curricula/curriculum/{curriculum_id}/cohorts",
     *     description="",
     *     @OA\Parameter(
     *     name="curriculum_id",
     *     required=true,
     *     in="path",
     *     description="id of the curriculum",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="List of all cohorts in a school")
     * )
     */
    public function list(Request $request)
    {
        $cohorts = Kohorte::all();
        foreach ($cohorts as $cohort)
        {
            $cohort["members_count"] = $cohort->members_count();
            $cohort->load("lehrplan");
            $cohort->load("studium");
            $cohort->load("schule");
        }
        return parent::createJsonResponse("cohorts of the school",false, 200, ["cohorts" => $cohorts]);
    }
    /**
     * @OA\Post(
     *     tags={"masterdata", "v1"},
     *     path="/api/v1/administration/curricula/curriculum/{curriculum_id}/cohorts",
     *     description="",
     *     @OA\Parameter(
     *     name="name",
     *     required=true,
     *     in="query",
     *     description="name of the cohort",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="school",
     *     required=true,
     *     in="path",
     *     description="id of the school",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="study",
     *     required=true,
     *     in="query",
     *     description="id of the study",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="plan",
     *     required=true,
     *     in="query",
     *     description="id of the lesson plan",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="year",
     *     required=true,
     *     in="query",
     *     description="id of the school year",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="days",
     *     required=true,
     *     in="query",
     *     description="days of study",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Create a new cohort")
     * )
     */
    public function add(Request $request)
    {
        $object = $request->object;
        if(!$object)
            return parent::createJsonResponse("No object given",true, 404);

        $curriculum = Lehrplan::findOrFail($object["lehrplan_id"]);
        $study = Studium::findOrFail($object["studium_id"]);
        $year = Schuljahr::findOrFail($object["schuljahr_id"]);
        $school= Schule::findOrFail($object["schule_id"]);

        // TODO: Check if school year is valid for that school

        $cohort = Kohorte::createWithAttributes($school, $study,$year);
        $cohort->lehrplan_id = $curriculum->id;
        $cohort->save();
        $cohort->load("members");
        return $this->detail($cohort->id, $request);
    }
    /**
     * @OA\Get(
     *     tags={"masterdata", "v1"},
     *     path="/api/v1/administration/curricula/curriculum/{curriculum_id}/cohorts/{cohort_id}",
     *     description="",
     *     @OA\Parameter(
     *     name="curriculum_id",
     *     required=true,
     *     in="path",
     *     description="id of the curriculum",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="cohort_id",
     *     required=true,
     *     in="path",
     *     description="id of the cohort",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Array of all teachers in a school in the system with additional information (masterdata)")
     * )
     */
    public function detail($cohort_id, Request $request)
    {
        $cohort = Kohorte::findOrFail($cohort_id);
        $cohort["members_count"] = $cohort->members_count();
        $cohort->load("lehrplan");
        $cohort->load("studium");
        $cohort->load("schule");
        $cohort->load("members");
        return parent::createJsonResponse("details for cohort",false, 200, ["cohort" => $cohort]);
    }
    /**
     * @OA\Post(
     *     tags={"masterdata", "v1"},
     *     path="/api/v1/administration/curricula/curriculum/{curriculum_id}/cohorts/{cohort_id}",
     *     description="",
     *     @OA\Parameter(
     *     name="curriculum_id",
     *     required=true,
     *     in="path",
     *     description="id of the curriculum",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="cohort_id",
     *     required=true,
     *     in="path",
     *     description="id of the cohort",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Update a cohort's name")
     * )
     */
    public function update($cohort_id, Request $request)
    {
        $cohort = Kohorte::findOrFail($cohort_id);
        /*if($cohort->lehrplan->id != $curriculum_id)
        {
            return $this->createJsonResponse("Cohort not found in curriculum.", true, 400);
        }*/

        $cohort->name = $request->input("name");
        $cohort->save();

        return $this->detail($cohort_id, $request);
    }


    public function delete($cohort_id, Request $request)
    {
        $cohort = Kohorte::findOrFail($cohort_id);
        if($cohort == null || $cohort->members()->count() > 0)
        {
            return parent::createJsonResponse("Die Kohorte hat noch Mitglieder",true, 400);
        }
        Klasse::where('type', '=', 'planning_group')->where('kohorte_id', '=', $cohort->id)->delete();

        $cohort->delete();
        return parent::createJsonResponse("deletion successful",false, 200);
    }
}

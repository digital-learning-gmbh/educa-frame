<?php

namespace App\Http\Controllers\API\V1\Administration\Masterdata;

use App\Http\Controllers\API\V1\Administration\AdministationApiController;
use App\Klasse;
use App\Kohorte;
use App\LehrplanEinheit;
use App\ModulExamExecution;
use App\Schuler;
use App\Schuljahr;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MasterdataCourseController extends AdministationApiController
{

    protected function addBlockingGroup(Request $request)
    {
        //
        //TODO
        /**
         *                  type : COURSE_TYPES.BLOCKING_GROUP,
                            year_id : yearId,
                            study_days : studyDays,
                            start : start,
                            end : end,
                            isCategoryChose : isCategoryChose,
                            lehrplanEinheitId : lehrplanEinheitId
         *
         * Example:
                            end	1635026400
                            isCategoryChose	true
                            lehrplanEinheitId	49
                            start	1632693600
                            study_days	"mo_tu"
                            type	"blocking_group"
                            year_id	11
         */

        $schoolyear = $request->input(["year_id"]);
        if (!$schoolyear)
            return $this->createJsonResponse("No year id was set", true, 400);

        if (!$request->input("lehrplanEinheitId"))
            return $this->createJsonResponse("No lehrplanEinheitId id was set", true, 400);

        if (!$request->input("fs"))
            return $this->createJsonResponse("No fs was set", true, 400);


        $schoolyear = Schuljahr::find($schoolyear);
        $klasse = Klasse::createBlockungsgruppe($schoolyear,
            LehrplanEinheit::find($request->input("lehrplanEinheitId")),
            $request->input("isCategoryChose"),
            Carbon::createFromTimestamp($request->input("start")),
            Carbon::createFromTimestamp($request->input("end")),
            $request->input("study_days"),
            $request->input("fs")
        );
        $klasse->load("schuler:id");
        return parent::createJsonResponse("klasse created", false, 200, ["schoolclass" =>
            $klasse]);
    }

    protected function addClusterGroup(Request $request)
    {
        //TODO
        /**
         *  type : COURSE_TYPES.CLUSTER_GROUP,
            year_id : yearId,
            name : name,
            course_ids : courseIds,
         */
        $schoolyear = $request->input(["year_id"]);
        if (!$schoolyear)
            return $this->createJsonResponse("No year id was set", true, 400);

        if (!$request->input("course_ids"))
            return $this->createJsonResponse("No courseIds was set", true, 400);

        $schoolyear = Schuljahr::find($schoolyear);
        $klasse = Klasse::createClusterGroup($schoolyear,$request->input("course_ids"));
        $klasse->load("schuler:id");
        return parent::createJsonResponse("klasse created", false, 200, ["schoolclass" =>
            $klasse]);
    }

    protected function addFreeGroup(Request $request)
    {
        /**
         *    type : COURSE_TYPES.FREE_GROUP,
              year_id : yearId,
              name : name
         */
        $schoolyear = $request->input(["year_id"]);
        if (!$schoolyear)
            return $this->createJsonResponse("No year id was set", true, 400);

        if (!$request->input("name"))
            return $this->createJsonResponse("No name was set", true, 400);


        $schoolyear = Schuljahr::find($schoolyear);
        $klasse = Klasse::createFreeGroup($schoolyear, $request->input("name"));
        $klasse->load("schuler:id");
        return parent::createJsonResponse("klasse created", false, 200, ["schoolclass" =>
            $klasse]);
    }

    protected function addPlanningGroup(Request $request)
    {
        $schoolyear = $request->input(["year_id"]);
        if (!$schoolyear)
            return $this->createJsonResponse("No year id was set", true, 400);

        if (!$request->input("kohorte_id"))
            return $this->createJsonResponse("No kohorte id was set", true, 400);

        $schoolyear = Schuljahr::find($schoolyear);
        $kohorte = Kohorte::find($request->input("kohorte_id"));
        $studientage = $request->input("study_days");

        $klasse = Klasse::createPlanungsKlasse($schoolyear, $kohorte, $studientage);
        $klasse->load("schuler:id");
        return parent::createJsonResponse("klasse created", false, 200, ["schoolclass" =>
            $klasse]);
    }

    public function add(Request $request)
    {
        $type = $request->input(["type"]);
        if(!$type)
            return $this->createJsonResponse("No type was set", true, 400);

        if($type == "planning_group")
        {
           return $this->addPlanningGroup($request);
        }
        if($type == "blocking_group")
        {
            return $this->addBlockingGroup($request);
        }
        if($type == "cluster_group")
        {
            return $this->addClusterGroup($request);
        }
        if($type == "free_group")
        {
            return $this->addFreeGroup($request);
        }

        return $this->createJsonResponse("type is not supported", true, 400);
    }

    public function details($course_id, Request $request)
    {
        $klasse = Klasse::find($course_id);
        $members = Schuler::find($klasse->getAllSchulerAttribute());
        foreach ($members as $member)
        {
            if($klasse->type == "cluster_group")
                $zeitraum = DB::table("klasse_schuler")->whereIn("klasse_id",$klasse->klassen->pluck("id"))
                ->where("schuler_id","=",$member->id)->first();
            else
                $zeitraum = DB::table("klasse_schuler")->where("klasse_id","=",$klasse->id)
                    ->where("schuler_id","=",$member->id)->first();
            $member->von = $zeitraum->from == null ? "-" : date("d.m.Y",strtotime($zeitraum->from));
            $member->bis = $zeitraum->until == null ? "-" : date("d.m.Y",strtotime($zeitraum->until));
        }
        $members->each->load("addinfo");
        $members->each->append("schule");
        $klasse->schuler = $klasse->getSchulerAttribute();
        $klasse->members_count = $members->count();
        $klasse->load("kohorte");
        $klasse->load("kohorte.studium");
        $klasse->load("klassen");
        $klasse->load("getLehrplan");
        $klasse->load("getLehrplan.studiumRelation");
        $klasse->load("lehrplanEinheit");

        return parent::createJsonResponse("klasse detail",false, 200, [ "schoolclass" =>
            $klasse, "members" => $members, "isCategory" => $klasse->lehrplanEinheit != null && $klasse->lehrplanEinheit->parent()->form == "category"]);
    }

    public function avaiableStudentsWithOpenStuff($course_id, Request $request)
    {
        $klasse = Klasse::find($course_id);
        $ids = [];
        if($klasse->type == "free_group")
            $ids = DB::table("schuler_schule")->where("schule_id","=",$klasse->schuljahr->schule->id)
                ->pluck("schuler_id");

        if($klasse->type == "planning_group")
            $ids = $klasse->kohorte->members()->pluck("schuler_id");


        if($klasse->type == "blocking_group" || $klasse->type == "special") {
            $lehrplan_ids = $klasse->getLehrplan()->pluck("lehrplan_id");
            $ids = DB::table("study_progress_entries")->whereIn("schuljahr_id",DB::table('schuljahrs')->where("name",'=',$klasse->schuljahr->name)->pluck("id"))
                    ->whereIn("kohorte_id",DB::table('kohortes')->whereIn("lehrplan_id",$lehrplan_ids)->pluck("id"))->pluck("schuler_id");
        }
        $student = Schuler::whereIn("id",$ids)->whereNotIn("id",DB::table("klasse_schuler")->where("klasse_id",'=',$klasse->id)->pluck("schuler_id"))->with("addinfo")->get();
        $student->each->append("schule");
        return parent::createJsonResponse("klasse studentn da",false, 200, [ "students" =>
            $student ]);
    }

    public function update($course_id, Request $request)
    {
        $klasse = Klasse::find($course_id);

        $start_date = Carbon::createFromTimestamp($request->input("start_date"));
        $end_date = Carbon::createFromTimestamp($request->input("end_date"));
        $memberIds = $request->input("member_ids");
        $notes = $request->input("notes");

        foreach ($memberIds as $memberId)
        {
            // suchen wir den Lümmel
            $schuler = Schuler::find($memberId);
            if($schuler != null)
            {
                DB::table('klasse_schuler')->where('klasse_id', '=', $klasse->id)->where('schuler_id', '=', $schuler->id)->delete();
                DB::table('klasse_schuler')->insert(
                    ['klasse_id' => $klasse->id, 'schuler_id' => $schuler->id, 'from' => $start_date->format("Y/m/d"), 'until' => $end_date->format("Y/m/d"), 'note' => $notes]
                );
            }
        }

        return $this->details($course_id,$request);
    }

    public function deleteMembers($course_id, Request $request)
    {
        $klasse = Klasse::find($course_id);
        $memberIds = $request->input("member_ids");
        foreach ($memberIds as $memberId)
        {
            DB::table('klasse_schuler')->where('klasse_id', '=', $klasse->id)->where('schuler_id', '=', $memberId)->delete();
        }

        return $this->details($course_id,$request);
    }

    public function delete($course_id, Request $request)
    {
        $klasse = Klasse::find($course_id);
        $examExecution = ModulExamExecution::where('klasse_id','=',$klasse->id)->get();
        $examExecution->each->delete();
        DB::table('klasse_schuler')->where("klasse_id",'=',$klasse->id)->delete();
        DB::table('study_progress_entries')->where("klasse_id",'=',$klasse->id)->update([
            "klasse_id" => null
        ]);
        $klasse->delete();

        return parent::createJsonResponse("klasse deleted",false, 200, [ "schoolclass" =>
            $klasse ]);
    }
}

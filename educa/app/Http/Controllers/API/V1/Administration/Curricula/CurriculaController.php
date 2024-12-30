<?php

namespace App\Http\Controllers\API\V1\Administration\Curricula;

use App\Fach;
use App\Http\Controllers\API\V1\Administration\AdministationApiController;
use App\Lehrplan;
use App\LehrplanEinheit;
use App\Module;
use App\ModulExam;
use App\Schule;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CurriculaController extends AdministationApiController
{
    //

    private $einheiten = [];
    private $idsFound = [];

    public function list(Request $request)
    {
        $school_id = $request->school_id;
        return parent::createJsonResponse("curricula",false, 200, [ "curricula" => Lehrplan::all()]);
    }

    public function add(Request $request)
    {
        $lehrplan = new Lehrplan;
        $lehrplan->name = $request->input("name");
        $lehrplan->studium_id = $request->input("study_id");
        $lehrplan->save();

        $lehrplan->createDefaultCategory();

        return parent::createJsonResponse("curricula created",false, 200, [ "curriculum" => $lehrplan]);
    }

    public function details($curriculum_id, Request $request)
    {
        $lehrplan = Lehrplan::find($curriculum_id);
        //$lehrplan->append('lehrplanEinheiten'); //doesnt work with sorting
        $lehrplan->lehrplanEinheiten = $this->lehrplanEinheitRecursiveHelper($lehrplan);
        if($lehrplan == null)
            return $this->createJsonResponse("curricula not found.", true, 404);

        return parent::createJsonResponse("curricula details",false, 200, [ "curriculum" => $lehrplan]);
    }

    private function lehrplanEinheitRecursiveHelper(&$lehrplan, $parent = null)
    {
        $lehrplanEinheiten = [];
        foreach($lehrplan->lehreinheitenRelation()->where("lehrplan_einheit_id", "=", $parent)->orderBy("position")->get() as $lehrplanEinheit)
        {
            $lehrplanEinheit->children_relation = $this->lehrplanEinheitRecursiveHelper($lehrplan, $lehrplanEinheit->id);
            $lehrplanEinheiten[] = $lehrplanEinheit;
        }
        return $lehrplanEinheiten;
    }

    public function update($curriculum_id, Request $request)
    {
        $lehrplan = Lehrplan::find($curriculum_id);
        if($lehrplan == null)
            return $this->createJsonResponse("curricula not found.", true, 404);

        $obj = $request->object;
        $lehrplan->validated = true;
        $lehrplan->name = $obj["name"];
        $lehrplan->date_from =  $obj["date_from"] ? Carbon::createFromTimestamp($obj["date_from"]) : null;
        $lehrplan->date_until = $obj["date_until"] ? Carbon::createFromTimestamp($obj["date_until"]) : null;
        $lehrplan->save();


        DB::transaction(function() use($lehrplan, $obj) {
            $ids_before = $lehrplan->lehreinheitenRelation()->pluck("id");
            $position = 0;
            foreach ($obj["lehrplanEinheiten"] as $o) {
                if ($o["form"] != "category_choose" && $o["form"] != "category" && $o["form"] != "direction_of_study") {
                    return $this->createJsonResponse("module not in category.", true, 400);
                }
                $credits = $this->updateRecursiveHelper($o, $lehrplan, $position);
                $position++;
            }
            $oldUnits = LehrplanEinheit::find($ids_before);
            $this->deleteUnits($oldUnits);
            // print_r(count($ids_before)."<br>");
          //  print_r(count($this->idsFound));
            $this->idsFound = [];
        });

        return $this->details($curriculum_id, $request);
    }

    private function deleteUnits($einheiten)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach($einheiten as $lehrplanEinheit)
        {
            if(!in_array($lehrplanEinheit->id, $this->idsFound)) {
                $lehrplanEinheit->delete();
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    private function updateRecursiveHelper($category, &$curriculum, $position, $parentId = null)
    {
        $lehrplanEinheit = LehrplanEinheit::find($category["id"]);
        if($lehrplanEinheit == null)
        {
            $lehrplanEinheit = new LehrplanEinheit;
        } else {
            $this->idsFound[] = $lehrplanEinheit->id;
        }
        $lehrplanEinheit->name = $category["object"]["name"];
        $lehrplanEinheit->lehrplan_id = $curriculum->id;;
        $lehrplanEinheit->lehrplan_einheit_id = $parentId;
        $lehrplanEinheit->form = $category["form"];
        $lehrplanEinheit->position = $position;
        if(array_key_exists("object", $category) && array_key_exists("additional_attributes", $category["object"]) )
           $lehrplanEinheit->additional_attributes = json_encode( $category["object"]["additional_attributes"]);

        $credits = 0;
        $modules = 0;
        $min_credits = 0;
        $max_credits = INF;
        $min_modules = 0;
        if(array_has($category["object"], "rules") && is_array($category["object"]["rules"]))
        {
            foreach ($category["object"]["rules"][0]["children"] as $rule) {
                switch ($rule["type"]) {
                    case "MIN_CREDITS":
                        $min_credits = $rule["value"];
                        break;
                    case "MAX_CREDITS":
                        $max_credits = $rule["value"];
                        break;
                    case "MIN_MODULES":
                        $min_modules = $rule["value"];
                        break;
                }
            }
            $lehrplanEinheit->rules = json_encode($category["object"]["rules"]);
        }

        $lehrplanEinheit->save();
        $this->einheiten[] = $lehrplanEinheit;

        if(array_has($category, "children") && count($category["children"]))
        {
            $childposition = 0;
            foreach($category["children"] as $child)
            {
                if($child["form"] == "module")
                {
                    $moduleLehrplanEinheit = LehrplanEinheit::find($child["id"]);
                    if($moduleLehrplanEinheit == null)
                    {
                        $moduleLehrplanEinheit = new LehrplanEinheit;
                    } else {
                        $this->idsFound[] = $moduleLehrplanEinheit->id;
                    }
                    $module = Module::find($child["object"]["id"]);
                    if($module == null)
                    {
                        $curriculum->validated = false;
                        $curriculum->save();
                    }

                    $moduleLehrplanEinheit->name = $child["object"]["name"];
                    if($moduleLehrplanEinheit->name == "")
                    {
                        $curriculum->validated = false;
                        $curriculum->save();
                    }
                    $moduleLehrplanEinheit->lehrplan_id = $curriculum->id;
                    $moduleLehrplanEinheit->lehrplan_einheit_id = $lehrplanEinheit->id;
                    $moduleLehrplanEinheit->form = "module";
                    $moduleLehrplanEinheit->module_id = $module->id;
                    $moduleLehrplanEinheit->position = $childposition;
                    $moduleLehrplanEinheit->save();
                    $this->einheiten[] = $moduleLehrplanEinheit;

                    $credits += $child["object"]["ects"];
                    $modules++;
                }
                else
                {
                    $credits += $this->updateRecursiveHelper($child, $curriculum, $childposition, $lehrplanEinheit->id);
                }
                $childposition++;
            }
        }

        if($modules < $min_modules || $credits < $min_credits || $credits > $max_credits)
        {
            $curriculum->validated = false;
            $curriculum->save();
        }

        return $credits;
    }

    public function updateModule($curriculum_id, $module_id, Request $request)
    {
        $lehrplan = Lehrplan::find($curriculum_id);
        if($lehrplan == null)
            return $this->createJsonResponse("curricula not found.", true, 404);

        $modul = Module::find($module_id);
        if($modul == null)
            return $this->createJsonResponse("modul not found.", true, 404);

        if($request->input("subjects"))
        {
            foreach ($request->input("subjects") as $subject_obj)
            {
                $fach = Fach::find($subject_obj["id"]);
                if($fach != null)
                {
                    $fach->saveSemesterInformation($module_id, $curriculum_id, $subject_obj["semester_occurence"]);
                }
            }
        }
        return parent::createJsonResponse("information updated",false, 200);
    }

    public function createAllocatedExam($curriculum_id, $module_id, Request $request)
    {
        $lehrplan = Lehrplan::find($curriculum_id);
        if($lehrplan == null)
            return $this->createJsonResponse("curriculum not found.", true, 404);

        $modul = Module::find($module_id);
        if($modul == null)
            return $this->createJsonResponse("module not found.", true, 404);

        $examId = $request->input("exam_id");
        if( !$examId )
            return $this->createJsonResponse("No exam id was given that day.", true,400);

//        if( !$request->input("school_ids") )
//            return $this->createJsonResponse("No school id was given or school not found.", true,400);
//
//        if( !$request->input("from") )
//            return $this->createJsonResponse("No from date was given.", true,400);
//
//        if( !$request->input("until") )
//            return $this->createJsonResponse("No until date was given.", true,400);

        /*
        if( DB::table('modul_exam_curiculum')->where(["lehrplan_id" => $lehrplan->id, "modul_exam_id" => $examId, "module_id" => $modul->id ])->count() > 0)
            return $this->createJsonResponse("Realtion exists.", true,400);
*/
        DB::beginTransaction();

        try {
            if($request->input("school_ids") != null) {
                foreach ($request->input("school_ids") as $school_id) {
                    if (!Schule::find($school_id)) {
                        throw new \Error();
                        return $this->createJsonResponse("school not found", true, 400);
                    }

                    if (ModulExam::find($examId)) {

                        DB::table('modul_exam_curiculum')->insert([
                            'lehrplan_id' => $lehrplan->id,
                            'module_id' => $modul->id,
                            'modul_exam_id' => $examId,
                            'from' => $request->input("from") ? Carbon::createFromTimestamp($request->input("from"))->toDateTimeString() : null,
                            'until' => $request->input("until") ? Carbon::createFromTimestamp($request->input("until"))->toDateTimeString(): null,
                            'school_id' => $school_id
                        ]);
                    }
                }
            } else {
                if (ModulExam::find($examId)) {

                    DB::table('modul_exam_curiculum')->insert([
                        'lehrplan_id' => $lehrplan->id,
                        'module_id' => $modul->id,
                        'modul_exam_id' => $examId,
                        'from' => $request->input("from") ? Carbon::createFromTimestamp($request->input("from"))->toDateTimeString() : null,
                        'until' => $request->input("until") ? Carbon::createFromTimestamp($request->input("until"))->toDateTimeString(): null,
                        'school_id' => null
                    ]);
                }
            }
            DB::commit();
            return parent::createJsonResponse("created",false, 200);
        } catch (\Exception $e)
        {
            DB::rollback();
        }

        return parent::createJsonResponse("module exam not found",true, 400);
    }

    public function updateAllocatedExam($curriculum_id, $module_id, $allocated_exam_id, Request $request)
    {
        $lehrplan = Lehrplan::find($curriculum_id);
        if($lehrplan == null)
            return $this->createJsonResponse("curriculum not found.", true, 404);

        $modul = Module::find($module_id);
        if($modul == null)
            return $this->createJsonResponse("module not found.", true, 404);

        $examId = $request->input("exam_id");
        if( !$examId )
            return $this->createJsonResponse("No exam id was given that day.", true,400);

//        if( !$request->input("school_id") )
//            return $this->createJsonResponse("No school id was given or school not found.", true,400);
//
//        if( !$request->input("from") )
//            return $this->createJsonResponse("No from date was given.", true,400);
//
//        if( !$request->input("until") )
//            return $this->createJsonResponse("No until date was given.", true,400);

        DB::beginTransaction();

        $school_id = $request->input("school_id");
        try {
//                if (!Schule::find($school_id))
//                {
//                    throw new \Error();
//                    return $this->createJsonResponse("school not found", true, 400);
//                }

                if (ModulExam::find($examId)) {

                    $entry = DB::table('modul_exam_curiculum')->where(["id" => $allocated_exam_id]);
                    if($entry->count() !== 1)
                        return $this->createJsonResponse("No such entry found.", true,400);
                    $entry->update([
                        'lehrplan_id' => $lehrplan->id,
                        'module_id' => $modul->id,
                        'modul_exam_id' => $examId,
                        'from' => $request->input("from") ? Carbon::createFromTimestamp($request->input("from"))->toDateTimeString() : null,
                        'until' => $request->input("until") ? Carbon::createFromTimestamp($request->input("until"))->toDateTimeString(): null,
                        'school_id' => $school_id ? $school_id : null,
                    ]);

                }
            DB::commit();
            return parent::createJsonResponse("updated",false, 200);
        } catch (\Exception $e)
        {
            DB::rollback();
        }

        return parent::createJsonResponse("module exam not found",true, 400);
    }

    public function deleteAllocatedExam($curriculum_id, $module_id, Request $request)
    {
        $id = $request->input("allocated_exam_id");
        if(!$id)
            return $this->createJsonResponse("allocated exam id is undefined.", true, 404);
        $lehrplan = Lehrplan::find($curriculum_id);
        if($lehrplan == null)
            return $this->createJsonResponse("curriculum not found.", true, 404);

        $module = Module::find($module_id);
        if($module == null)
            return $this->createJsonResponse("module not found.", true, 404);

        $q = DB::table('modul_exam_curiculum')->where([
            "lehrplan_id" => $lehrplan->id,
            "module_id" =>$module->id,
            "id" => $id]);
        if($q->count() == 0)
            return parent::createJsonResponse("error. not existent.",true, 400);

        $q->delete();

        return parent::createJsonResponse("deleted",false, 200);
    }

}

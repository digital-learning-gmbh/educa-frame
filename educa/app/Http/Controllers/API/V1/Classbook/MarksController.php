<?php

namespace App\Http\Controllers\API\V1\Classbook;

use App\Http\Controllers\API\ApiController;
use App\Note;
use App\NotenCache;
use App\Schuler;
use App\Schuljahr;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarksController extends ApiController
{
    public function markForStudent(Request $request)
    {
        $student_id = $request->input("student_id");

        $schuler = Schuler::find($student_id);
        $schule = $schuler->schulen->first();
        $schuljahr = $schule->getCurrentSchoolYear();
        $currentStudy = $schuler->getCurrentStudyInformation($schuljahr->id);
        if ($currentStudy == null) {
            $currentStudy = $schuler->getLastStudyInformation();
        }

        if($currentStudy == null || $currentStudy->lehrplan() == null)
            return parent::createJsonResponse("grades for student",false, 200, [ "grades" => [], "notenCache" => null]);

        $noten = Note::with("schuljahr")->where('schuler_id','=',$student_id)->whereIn('model_type',["modul"])
            ->where("status","=","public")->orderByRaw('model_type, model_id, version DESC, datum DESC')
            ->whereIn("model_id",DB::table("modul_fach_curiculum")
                ->where("lehrplan_id", "=", $currentStudy->lehrplan()->id)
                ->pluck("module_id")
            )->where("consider_current_curriculum","=",1)
            ->get();

        $noten->each->append("belongsObject");

        $schuljahr_ids = Schuljahr::where("start","<=",Carbon::now())->where("ende",">=",Carbon::now())->pluck("id");

        $lastNotenCache = NotenCache::where("schuler_id","=",$student_id)->whereIn("schuljahr_id",$schuljahr_ids)->first();
        if($lastNotenCache == null)
            $lastNotenCache = NotenCache::where("schuler_id","=",$student_id)->orderBy("ects_sum","DESC")->first();

        //$noten->each->partNoten->each->append('linkedSubjects');
        return parent::createJsonResponse("grades for student",false, 200, [ "grades" => $noten, "notenCache" => $lastNotenCache]);
    }
}

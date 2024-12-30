<?php


namespace App\Http\Controllers\API\V1\Administration\Widgets;
use App\Fach;
use App\Http\Controllers\API\UnterrichtController;
use App\Klasse;
use App\Lehrer;
use App\LessonPlan;
use App\SchuljahrEntwurf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TeacherCurriculaWidget extends Widget
{
    /**
     * @OA\Get (
     *     tags={"administration", "v1", "widgets", "planning"},
     *     path="/api/v1/administration/widgets/planning/teacher",
     *     description="",
     *     @OA\Parameter(
     *     name="token",
     *     required=true,
     *     in="query",
     *     description="jwt token",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="darft",
     *     required=true,
     *     in="query",
     *     description="id of the darft",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="course",
     *     required=true,
     *     in="query",
     *     description="id of the course",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="end",
     *     required=true,
     *     in="query",
     *     description="timestamp of the end",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="start",
     *     required=true,
     *     in="query",
     *     description="timestamp of the start frame",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Returns a table of balance for planing containing information about the teacher")
     * )
     */
    public function dozent(Request $request)
    {
        $data = [];

        $entwurf = SchuljahrEntwurf::findOrFail($request->input("draft"));
        $start = Carbon::createFromTimestamp($request->input("start"));
        $end = Carbon::createFromTimestamp($request->input("end"));
        $selectKlasse = Klasse::findOrFail($request->input("course"));

        $unterrichtsplan = UnterrichtController::getUnterrichtInternalWithoutFormat($start, $end, "schoolclass", $selectKlasse->id, $entwurf);
        $dozentUnterricht = [];

        $defaultUE = 60;

        $lehrplan = null;
        if($selectKlasse->getLehrplan->count() == 1) {
            $lehrplan = $selectKlasse->getLehrplan->first();
            $defaultUE = $lehrplan->dauer_UE ? $lehrplan->dauer_UE : 60;
        }
        if($defaultUE == 0)
            $defaultUE = 60;

        foreach($unterrichtsplan as $unterricht) {
            if ($unterricht instanceof LessonPlan) {
                foreach ($unterricht->dozent->pluck("id") as $dozent_id) {
                    if (!array_key_exists($dozent_id, $dozentUnterricht)) {
                        $dozentUnterricht[$dozent_id] = [];
                    }
                    $dozentUnterricht[$dozent_id][] = $unterricht;
                }

                if ($unterricht->dozent->count() == 0) {
                    $dozent_id = "-1";
                    if (!array_key_exists($dozent_id, $dozentUnterricht)) {
                        $dozentUnterricht[$dozent_id] = [];
                    }
                    $dozentUnterricht[$dozent_id][] = $unterricht;
                }
            }
        }


        foreach ($dozentUnterricht as $key=>$value) {
            $node = [];
            $node["key"] = $key;
            $lehrer = Lehrer::find($key);
            $node["dozent"] = "<i>Ohne Dozent</i>";
            if($lehrer != null)
            {
                $node["dozent"] = $lehrer->displayName;
            }
            $node["fach"] = "<b><i>Gesamt</i></b>";
            $node["ist_zeitraum"] = 0;
            $fachListe = [];
            foreach ($value as $lesson) {
                $startLesson = new Carbon($lesson->getStartDate());
                $endLesson = new Carbon($lesson->getEndDate());
                if($lesson->deviant_ue != null && $lesson->deviant_ue != 0)
                {
                    $time =  $lesson->deviant_ue*$defaultUE;
                    $node["ist_zeitraum"] += $time;
                } else {
                    $node["ist_zeitraum"] += $startLesson->diffInMinutes($endLesson);
                }
                if (!array_key_exists($lesson->fach_id, $fachListe)) {
                    $fachListe[$lesson->fach_id] = [];
                }
                $fachListe[$lesson->fach_id][]  = $lesson;
            }
            $node["ist_zeitraum"] = "<b>".round($node["ist_zeitraum"] / $defaultUE)."</b>";
            $node["dozent"] = "<b>".$node["dozent"]."</b>";
            foreach ($fachListe as $key2=>$fachs)
            {
                $node["fach"] .= "<br>".$fachs[0]->fachname();
                $summe = 0;
                foreach ($fachs as $lesson2) {
                    $startLesson = new Carbon($lesson2->getStartDate());
                    $endLesson = new Carbon($lesson2->getEndDate());
                    if($lesson->deviant_ue != null && $lesson->deviant_ue != 0)
                    {
                        $time =  $lesson->deviant_ue*$defaultUE;
                        $summe += $time;
                    } else {
                        $summe += $startLesson->diffInMinutes($endLesson);
                    }
                }
                $node["ist_zeitraum"] .= "<br>".round($summe / $defaultUE);
            }
            $data[] = $node;
        }

        return parent::createJsonResponseStatic('', false, 200,["data" => $data]);
    }

    /**
     * @OA\Get (
     *     tags={"administration", "v1", "widgets", "planning"},
     *     path="/api/v1/administration/widgets/planning/teacherSubject",
     *     description="",
     *     @OA\Parameter(
     *     name="token",
     *     required=true,
     *     in="query",
     *     description="jwt token",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="darft",
     *     required=true,
     *     in="query",
     *     description="id of the darft",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="teacher",
     *     required=true,
     *     in="query",
     *     description="id of the teacher",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="end",
     *     required=true,
     *     in="query",
     *     description="timestamp of the end",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="start",
     *     required=true,
     *     in="query",
     *     description="timestamp of the start frame",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Returns a table containing subject information about the teacher")
     * )
     */
    public function dozentFach(Request $request)
    {
        $data = [];

        $entwurf = SchuljahrEntwurf::findOrFail($request->input("draft"));
        $start = Carbon::createFromTimestamp($request->input("start"));
        $end = Carbon::createFromTimestamp($request->input("end"));
        $selectKlasse = Lehrer::findOrFail($request->input("teacher"));

        $defaultUE = 60;

        if ($defaultUE == 0)
            $defaultUE = 60;

        $unterrichtsplan = UnterrichtController::getUnterrichtInternalWithoutFormat($start, $end, "teacher", $selectKlasse->id, $entwurf);
        $dozentUnterricht = [];

        foreach($unterrichtsplan as $unterricht) {
            if ($unterricht instanceof LessonPlan) {
                $dozent_id = $unterricht->fach_id;
                if ($unterricht->fach_id == null) {
                    $dozent_id = "-1";
                }
                if (!array_key_exists($dozent_id, $dozentUnterricht)) {
                    $dozentUnterricht[$dozent_id] = [];
                }
                $dozentUnterricht[$dozent_id][] = $unterricht;
            }
        }


        foreach ($dozentUnterricht as $key=>$value) {
            $node = [];
            $node["key"] = $key;
            $lehrer = Fach::find($key);
            $node["subject"] = "";
            if($lehrer != null)
            {
                $node["subject"] = $lehrer->name;
            }
            $node["is_timespan"] = 0;
            foreach ($value as $lesson) {
                $startLesson = new Carbon($lesson->getStartDate());
                $endLesson = new Carbon($lesson->getEndDate());
                if($lesson->deviant_ue != null && $lesson->deviant_ue != 0)
                {
                    $time =  $lesson->deviant_ue*$defaultUE;
                    $node["is_timespan"] += $time;
                } else {
                    $node["is_timespan"] += $startLesson->diffInMinutes($endLesson);
                }
                $node["is_timespan"] = round($node["is_timespan"] / $defaultUE);
            }
            $data[] = $node;
        }

        return parent::createJsonResponseStatic('', false, 200,["data" => $data]);
    }

}

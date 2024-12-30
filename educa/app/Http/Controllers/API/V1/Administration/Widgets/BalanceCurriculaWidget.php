<?php


namespace App\Http\Controllers\API\V1\Administration\Widgets;

use App\Board;
use App\Fach;
use App\Http\Controllers\API\UnterrichtController;
use App\Klasse;
use App\KlassenbuchEintrag;
use App\Lehrer;
use App\LessonPlan;
use App\LessonSection;
use App\SchuljahrEntwurf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Mexitek\PHPColors\Color;

class BalanceCurriculaWidget extends Widget
{
    /**
     * @OA\Get (
     *     tags={"administration", "v1", "widgets", "planning"},
     *     path="/api/v1/administration/widgets/planning/balance",
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
     *     name="section",
     *     required=false,
     *     in="query",
     *     description="id of the section, optional",
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
     *     @OA\Response(response="200", description="Returns a table of balance for planing")
     * )
     */
    public function sollist(Request $request)
    {
        $data = [];
        $entwurf = SchuljahrEntwurf::findOrFail($request->input("draft"));

        $start = Carbon::createFromTimestamp($request->input("start"));
        $end = Carbon::createFromTimestamp($request->input("end"));
        $selectKlasse = Klasse::findOrFail($request->input("course"));

        $defaultUE = 60;

        if ($defaultUE == 0)
            $defaultUE = 60;

        $map = [];

        foreach ($selectKlasse->getSubjects() as $fach) {
            if ($fach != null) {
                if (!array_key_exists($fach->id, $map)) {
                    $map[$fach->id] = [];
                    $map[$fach->id]["fach"] = $fach;
                    $map[$fach->id]["anzahl"] = 0;
                }
                $map[$fach->id]["anzahl"] += $fach->duration;
            }

        }

        $unterrichtsplan = UnterrichtController::getUnterrichtInternalWithoutFormat($start, $end, "schoolclass", $selectKlasse->id, $entwurf);
        $fachToUnterricht = [];

        foreach ($unterrichtsplan as $unterricht) {
            if ($unterricht instanceof LessonPlan) {
                $fach_id = $unterricht->fach_id;
                if ($unterricht->fach_id == null) {
                    $fach_id = "-1";
                }
                if (!array_key_exists($fach_id, $fachToUnterricht)) {
                    $fachToUnterricht[$fach_id] = [];
                }
                $fachToUnterricht[$fach_id][] = $unterricht;
            }
        }

        $keys = [];

        foreach ($fachToUnterricht as $key => $value) {
            $node = [];
            $node["key"] = $key;
            $keys[] = $key;
            if ($key == "-1") {
                $node["fach"] = "Kein Fach";
            } else {
                $node["fach"] = Fach::findOrFail($key)->shortName;
            }

            $node["ist_zeitraum"] = 0;
            $node["soll_zeitraum"] = 0;

            if (array_key_exists($key, $map)) {
                $node["soll_zeitraum"] = $map[$key]["anzahl"];
            }

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
            }

            if ($defaultUE == 0 || $defaultUE == null)
                $defaultUE = 60;

            $node["diff"] = round($node["ist_zeitraum"] / $defaultUE - $node["soll_zeitraum"]);
            $node["ist_zeitraum"] = round(($node["ist_zeitraum"]) / $defaultUE);

            if ($node["diff"] > 0) {
                $node["diff"] = "+" . $node["diff"];
            }
            $data[] = $node;
        }

        foreach ($map as $key => $value) {
            $node = [];
            $node["key"] = $key;
            if (!in_array($key, $keys)) {
                $node["fach"] = $value["fach"]->shortName;
                $node["ist_zeitraum"] = 0;
                $node["soll_zeitraum"] = $value["anzahl"];
                $node["diff"] = $node["ist_zeitraum"] / $defaultUE - $node["soll_zeitraum"];
                $data[] = $node;
            }
        }

        return parent::createJsonResponseStatic('', false, 200, ["data" => $data]);
    }

    private function getColorClass($value)
    {
        if ($value < 20) {
            return "colorCell bg-danger  text-white";
        }
        if ($value < 40) {
            return "colorCell bg-warning";
        }
        if ($value == 100) {
            return "colorCell bg-success";
        }
        return "colorCell bg-light";
    }

    /**
     * @OA\Get (
     *     tags={"administration", "v1", "widgets", "planning"},
     *     path="/api/v1/administration/widgets/planning/shortcuts",
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
     *     name="type",
     *     required=true,
     *     in="query",
     *     description="type of the object: teacher, schoolclass",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="id",
     *     required=true,
     *     in="query",
     *     description="id of the object",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="section",
     *     required=false,
     *     in="query",
     *     description="id of the section, only if we have a schoolclass",
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
     *     @OA\Response(response="200", description="Returns a table of balance for planing")
     * )
     */
    public function shortCuts(Request $request)
    {
        $data = [];

        $entwurf = SchuljahrEntwurf::findOrFail($request->input("draft"));
        $start = Carbon::createFromTimestamp($request->input("start"));
        $end = Carbon::createFromTimestamp($request->input("end"));

        $type = $request->input("type", "schoolclass");
        if ($type == "teacher") {
            $teacher = Lehrer::find($request->input("id"));
            $fachKey = [];
            foreach ($teacher->faecher as $fach) {
                if (!in_array($fach->id, $fachKey)) {
                    $fachKey[] = $fach->id;
                    $obj = [];
                    $obj["ue"] = 1;
                    $obj["fach"] = $fach;
                    $obj["eventTextColor"] = "#fff";
                    try {
                        $color = new Color($obj["fach"]->color);
                        if ($color->isLight()) {
                            $obj["eventTextColor"] = "#000";
                        }
                    } catch (\Exception $exception) {
                        //
                    }
                    $data[] = $obj;
                }
            }
        } else if ($type == "schoolclass") {
            $selectKlasse = Klasse::findOrFail($request->input("id"));

            $unterrichtsplan = UnterrichtController::getUnterrichtInternalWithoutFormat($start, $end, "schoolclass", $selectKlasse->id, $entwurf);
            $fachToUnterricht = [];
            foreach ($unterrichtsplan as $unterricht) {
                if ($unterricht instanceof LessonPlan) {
                    $fach_id = $unterricht->fach_id;
                    if ($unterricht->fach_id == null) {
                        $fach_id = "-1";
                    }
                    if (!array_key_exists($fach_id, $fachToUnterricht)) {
                        $fachToUnterricht[$fach_id] = 0;
                    }
                    $startLesson = new Carbon($unterricht->getStartDate());
                    $endLesson = new Carbon($unterricht->getEndDate());
                    $fachToUnterricht[$fach_id] += $startLesson->diffInMinutes($endLesson);
                }
            }

            foreach ($selectKlasse->getLehrplan as $lehrplan) {
                $map = [];
                foreach ($selectKlasse->getSubjects() as $fach) {
                    if ($fach != null) {
                        if (!array_key_exists($fach->id, $map)) {
                            $map[$fach->id] = [];
                            $map[$fach->id]["fach"] = $fach;
                            $map[$fach->id]["anzahl"] = 0;
                        }
                        $map[$fach->id]["anzahl"] += $fach->duration;
                    }

                }
                foreach ($map as $key => $value) {
                    if (array_key_exists($key, $fachToUnterricht)) {
                        $value["anzahl"] = $value["anzahl"] - $fachToUnterricht[$key];
                    }
                }


                foreach ($map as $key => $value) {
                    $finalUE = $value["anzahl"];
                    if (array_key_exists($key, $fachToUnterricht)) {
                        $finalUE -= $fachToUnterricht[$key];
                    }
                    $finalUE = $finalUE;
                    if ($finalUE > 0) {

                        $obj = [];
                        $obj["ue"] = $finalUE;
                        $obj["fach"] = $value["fach"];
                        $obj["eventTextColor"] = "#fff";
                        try {
                            $color = new Color($obj["fach"]->color);
                            if ($color->isLight()) {
                                $obj["eventTextColor"] = "#000";
                            }
                        } catch (\Exception $exception) {
                            //
                        }
                        $data[] = $obj;
                    }
                }
            }
        }
        return parent::createJsonResponseStatic('', false, 200, ["data" => $data]);
    }
}

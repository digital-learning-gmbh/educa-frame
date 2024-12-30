<?php

namespace App\Http\Controllers\API\V1\Administration\Timetable;

use App\Fach;
use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\API\UnterrichtController;
use App\Klasse;
use App\Lehrer;
use App\LessonPlan;
use App\Models\Devices\Ressource;
use App\Raum;
use App\Schule;
use App\SchuljahrEntwurf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TeachingController extends ApiController
{
    /**
     * @OA\Get(
     *     tags={"timetable", "teaching", "v1", "administration"},
     *     path="/api/v1/timetable/teaching",
     *     description="",
     *     @OA\Parameter(
     *     name="start",
     *     required=true,
     *     in="query",
     *     description="The first date of that timetable data should be returned",
     *       @OA\Schema(
     *         type="datetime"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="end",
     *     required=true,
     *     in="query",
     *     description="The last date of that timetable data should be returned",
     *       @OA\Schema(
     *         type="datetime"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="id",
     *     required=true,
     *     in="query",
     *     description="The id, for that lesson data should returned",
     *       @OA\Schema(
     *         type="integer"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="type",
     *     required=true,
     *     in="query",
     *     description="The type of the id, for example teacher, schoolclass or room",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="draft",
     *     required=false,
     *     in="query",
     *     description="ID of the draft, optional otherwise the id will be automatically selected",
     *       @OA\Schema(
     *         type="integer"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Array of lesson data, timetable")
     * )
     */
    public function generateTimetable(Request $request)
    {
        $start = Carbon::createFromTimestamp( $request->input("start") )->toDateTime();
        $end =  Carbon::createFromTimestamp( $request->input("end") )->toDateTime();
        $type = strtolower($request->get("type"));
        $ids = explode(",",$request->get("ids"));

        $result = [];
        // if we have a fixed entwurf, everthing is fine.
        if ($request->has("draft")) {
            $entwurf = $request->get("draft");
            $entwurf = SchuljahrEntwurf::find($entwurf);
            if ($entwurf != null) {
                foreach ($ids as $id) {
                    $result = array_merge($result, UnterrichtController::getUnterrichtInternal($start, $end, $type, $id, $entwurf, $request->input("breaks", false), $request->input("vacation", true), $request->input("external_booking", false)));
                }
                return parent::createJsonResponse("timetable generated", false, 200, ["events" => $result]);
            }
        }
        foreach ($ids as $id) {
            $result = array_merge($result, UnterrichtController::getUnterrichtWithoutEntwurf($start, $end, $id, $type, $request->input("breaks", false), $request->input("vacation", true), $request->input("external_booking", false)));
        }
        return parent::createJsonResponse("timetable generated",false, 200,[ "events" => $result]);
    }



    public function teachers(Request $request)
    {
        if (!$request->has("school_id")) {
            $school_id = Schule::get()->first();
        } else {
            $school_id = Schule::findOrFail($request->input("school_id"));
        }

        if ($request->input("q") == "") {
            $raume = Lehrer::orderBy('lastname')->orderBy('firstname')->get();
        } else {
            $raume = Lehrer::search($request->input("q"))->orderBy('lastname')->orderBy('firstname')->get();
        }
        $result = [];
        $check = false;
        if ($request->has("subject_id")) {
            $schoolClass = Fach::find($request->input("subject_id"));
            if ($schoolClass != null) {
                $check = true;
                $subjects = $schoolClass->lehrer;
            }
        }

        $bestFit = [];
        $secondFit = [];
        foreach ($raume as $raum) {
            $element = [];
            $element["id"] = $raum->id;
            $element["name"] = $raum->displayName;

            if (in_array($school_id->id, $raum->schulen()->pluck("schule_id")->toArray())) {
                if (!$check || $subjects->contains($raum)) {
                    $bestFit[] = $element;
                } else {
                    $secondFit[] = $element;
                }
            }
        }

        $element = [];
        $element["text"] = "Passende Dozenten";
        $element["teachers"] = $bestFit;
        $result[] = $element;

        $element = [];
        $element["text"] = "Andere Dozenten";
        $element["teachers"] = $secondFit;
        $result[] = $element;
        return parent::createJsonResponse("ok",false, 200,["teachers" => $result]);
    }


    public function subjects(Request $request)
    {
        if(!$request->has("school_id")) {
            $school_id = Schule::get()->first();
        } else {
            $school_id = Schule::findOrFail($request->input("school_id"));
        }

        $studiumIds = $school_id->studiengange->pluck("id");

        if($request->input("q") == "")
        {
            $sbjcts = Fach::whereHas('studies', function($q) use($studiumIds) {
                $q->whereIn('fach_studium.studium_id', $studiumIds);
            })->orderBy('name')->get();
        } else {
            $sbjcts = Fach::whereHas('studies', function($q) use($studiumIds) {
                $q->whereIn('fach_studium.studium_id', $studiumIds);
            })->where('name', 'LIKE', '%'.$request->input("q").'%')->orderBy('name')->get();
        }
        $result = [];
        $subjects = [];
        $check = false;
        if($request->has("teacher"))
        {
            $schoolClass = Lehrer::find($request->input("teacher"));
            if($schoolClass != null)
            {
                $check = true;
                $subjects = $schoolClass->faecher;
            }
        }

        $bestFit = [];
        $secondFit = [];
        foreach ($sbjcts as $s) {
            $element = [];
            $element["id"] = $s->id;
            $element["name"] = $s->abk ? $s->abk.":".$s->name : $s->name;
            if (!$check || $subjects->contains($s)) {
                $bestFit[] = $element;
            } else {
                $secondFit[] = $element;
            }
        }

        $element =[];
        $element["text"] = "Passende Fächer";
        $element["subjects"] = $bestFit;
        $result[] = $element;

        $element =[];
        $element["text"] = "Andere Fächer";
        $element["subjects"] = $secondFit;
        $result[] = $element;
        return parent::createJsonResponse("ok",false, 200,["subjects" => $result]);
    }

    public function devices(Request $request)
    {
        if($request->input("q") == "")
        {
            $raume = Ressource::all();
        } else {
            $raume = Ressource::where('name', 'LIKE', '%'.$request->input("q").'%')->get();
        }
        $result = [];

        $bestFit = [];
        foreach ($raume as $raum) {
            $element = [];
            $element["id"] = $raum->id;
            $element["text"] = $raum->name;
            $bestFit[] = $element;
        }


        return parent::createJsonResponse("ok",false, 200,["devices" => $bestFit]);
    }

    public function rooms(Request $request)
    {
        if(!$request->has("school_id")) {
            $school_id = Schule::get()->first();
        } else {
            $school_id = Schule::findOrFail($request->input("school_id"));
        }

        if(!$request->has("q")  || $request->input("q") == "")
        {
            $raume = Raum::orderBy('name')->get();
        } else {
            $raume = Raum::search($request->input("q"))->orderBy('name')->get();
        }
        $result = [];
        $count = 0;
        if($request->has("lesson_plan_id")) {
            // load count from lessonplan object
            $lessonPlan = LessonPlan::find($request->input("lesson_plan_id"));
            if($lessonPlan != null)
            {
                foreach ($lessonPlan->klassen as $klasse)
                {
                    $count += $klasse->schulerAktuell()->count();
                }
            }
        }

        $bestFit = [];
        $secondFit = [];
        foreach ($raume as $raum) {
            $element = [];
            $element["id"] = $raum->id;
            $element["name"] = $raum->name;
            if (in_array($school_id->id, $raum->schulen()->pluck("schule_id")->toArray())) {
                if ($count <= $raum->size) {
                    $bestFit[] = $element;
                } else {
                    $secondFit[] = $element;
                }
            }
        }

        $element =[];
        $element["text"] = "Passende Räume";
        $element["rooms"] = $bestFit;
        $result[] = $element;

        $element =[];
        $element["text"] = "Weitere Räume";
        $element["rooms"] = $secondFit;
        $result[] = $element;

        return parent::createJsonResponse("ok",false, 200,["rooms" => $result]);
    }
}

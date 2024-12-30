<?php

namespace App\Http\Controllers\API\V1\Administration\Timetable;

use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\API\UnterrichtController;
use App\Klasse;
use App\LessonPlan;
use App\Schuljahr;
use App\SchuljahrEntwurf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanningController extends ApiController
{

    /**
     * @OA\Get(
     *     tags={"sections", "v1"},
     *     path="/api/v1/administration/timtable/timeframe",
     *     description="",
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
     *     name="type",
     *     required=true,
     *     in="query",
     *     description="type of the object: schoolclass, room, teacher",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Parameter(
     *     name="year",
     *     required=true,
     *     in="query",
     *     description="id of the schoolyear",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Array of all sections of a course")
     * )
     */
    public function getTimeFrameForPlanning(Request $request)
    {
        $type = $request->input("type", "schoolclass");
        $id = $request->input("id");

        $schuljahr = Schuljahr::findOrFail($request->input("year"));
        $startDate = date('Y-m-d H:i:00',strtotime($schuljahr->start));
        $minStart = $schuljahr->start;
        $endDate = $schuljahr->ende;

        $showWeekend = $schuljahr->getEinstellungen("showWeekend");
        $showWeekend = !($showWeekend == "false") && $showWeekend == "true";

        $sections = [];
        if ($type == "schoolclass") {
            $selectKlasse = Klasse::findOrFail($id);

            // 1. Falls die Klasse begrenzt ist
            if ($selectKlasse->von != null) {
                $startDate = Carbon::parse($selectKlasse->von);
                $minStart = $startDate;
            }
            if ($selectKlasse->bis != null) {
                $endDate = Carbon::parse($selectKlasse->bis)->addDay();
            }

            // 2. Falls die Klasse Theorie Abschnitte hat
            foreach ($selectKlasse->theorieAbschnitte as $theorieAbschnitt) {
                $section = [];
                $section["section"] = $theorieAbschnitt;
                $section["defaultDate"] = $theorieAbschnitt->begin;
                $section["startDate"] = $theorieAbschnitt->begin;
                $section["endDate"] = Carbon::parse($theorieAbschnitt->end)->addDay();
                $sections[] = $section;
            }
            if(count($sections) == 0)
            {
                $sections =[ [ "section" => [ "id" => "-1", "name" => "Kurszeitraum"], "defaultDate" => $minStart, "endDate" => $endDate, "startDate" => $startDate ] ];
            }
            return parent::createJsonResponse("calculated timeframe is",false, 200,[  "settings" => [ "showWeekend" => $showWeekend  ],"sections" => $sections ]);
        }

        return parent::createJsonResponse("calculated timeframe is",false, 200,[ "settings" => [ "showWeekend" =>$showWeekend ], "sections" => [ [ "section" => [ "id" => "-1", "name" => "Semester"], "defaultDate" => $minStart, "endDate" => $endDate, "startDate" => $startDate ] ] ]);
    }

    // Helper methods


    public function copyToNextWeek(Request $request)
    {

        /**
         *
         *  TODO :
         *
         *  $request->lessonplan_id
         */
        $lp = LessonPlan::findOrFail($request->input("lessonplan_id"));

        $copy = $lp->replicate();

        $start = $lp->getStartDate();
        $end = $lp->getEndDate();

        $start->modify("+1 week");
        $end->modify("+1 week");


        $sectionStart =  Carbon::createFromTimestamp($request->input("section_start"));
        $sectionEnd =  Carbon::createFromTimestamp($request->input("section_end"));

        if($sectionStart->isAfter($start) || $sectionEnd->isBefore($end))
            return $this->createJsonResponse("Der Kopie-Bereich ist außerhalb des Unterrichtsbereich", true, 200);

        $copy->startDate = $start;
        $copy->endDate = $end;
        $copy->save();
        $copy->dozent()->sync($lp->dozent->pluck("id"));
        $copy->klassen()->sync($lp->klassen->pluck("id"));
        $copy->raum()->sync($lp->raum->pluck("id"));
        foreach ($lp->merkmale as $merkmal)
        {
            $copy->setMerkmal($merkmal->key,$merkmal->value);
        }
        return parent::createJsonResponse("copied", false, 200, ["copy" => $copy]);
    }

    public function copyToNextDay(Request $request)
    {

        /**
         *
         *  TODO :
         *
         *  $request->lessonplan_id
         */

        $lp = LessonPlan::findOrFail($request->input("lessonplan_id"));

        $copy = $lp->replicate();

        $start = $lp->getStartDate();
        $end = $lp->getEndDate();

        if ($request->input("toNextWeek") === true) {
            $start->modify("+1 week");
            $end->modify("+1 week");
        } else {
            if ($start->format('N') == 5) //it is friday
            {
                $start->modify("+2 day");
                $end->modify("+2 day");
            }
            $start->modify("+1 day");
            $end->modify("+1 day");
        }

        $sectionStart =  Carbon::createFromTimestamp($request->input("section_start"));
        $sectionEnd =  Carbon::createFromTimestamp($request->input("section_end"));

        if($sectionStart->isAfter($start) || $sectionEnd->isBefore($end))
            return $this->createJsonResponse("Der Kopie-Bereich ist außerhalb des Unterrichtsbereich", true, 200);

        $copy->startDate = $start;
        $copy->endDate = $end;
        $copy->save();
        $copy->dozent()->sync($lp->dozent->pluck("id"));
        $copy->klassen()->sync($lp->klassen->pluck("id"));
        $copy->raum()->sync($lp->raum->pluck("id"));

        if($copy->isManualStudents)
            $copy->students()->sync($lp->students->pluck("id"));

        foreach ($lp->merkmale as $merkmal)
        {
            $copy->setMerkmal($merkmal->key,$merkmal->value);
        }
        return parent::createJsonResponse("copied", false, 200, ["copy" => $copy]);
    }

    public function copyWeek(Request $request)
    {
        $start =  Carbon::createFromTimestamp($request->input("start"))->toDateTime();
        $end =  Carbon::createFromTimestamp($request->input("end"))->toDateTime();
        $startCopy =  Carbon::createFromTimestamp($request->input("new_start"));
        //return response()->json($startCopy);
        $sectionStart =  Carbon::createFromTimestamp($request->input("section_start"));
        $sectionEnd =  Carbon::createFromTimestamp($request->input("section_end"));

        if(!($sectionStart->isBefore($startCopy) && ($sectionEnd->isAfter($startCopy))))
            return $this->createJsonResponse("Der Kopie-Bereich ist außerhalb des Unterrichtsbereich", true, 200);

        $startCopy = $startCopy->toDateTime();
        //$interval = $start->diff($startCopy);
        //$weekDiff = floor($interval->d/7);
        /*if($weekDiff <=0)
        {
            return response()->json(["status" => "Der Unterricht der zu kopierender Woche muss um mindestens eine Woche kopiert werden!"]);;
        }*/
        //$type = strtolower($request->get("type"));
        $id = Klasse::findOrFail($request->input("course_id"))->id;
        $entwurf = $request->input("draft_id");
        $entwurf = SchuljahrEntwurf::findOrFail($entwurf);
        $events = UnterrichtController::getUnterrichtInternalWithoutFormat($start, $end, "schoolclass", $id, $entwurf);

        foreach ($events as $event) {
            if (UnterrichtController::getLessonTyp($event) === "lessonPlan") {
                if ($event->recurrenceType === "never") {//only copy non recurrent events
                    $lp = LessonPlan::findOrFail($event->id);
                    $copy = $lp->replicate();
                    switch ($lp->getStartDate()->format('N')) {

                        case 1://mon
                            $copyStart = $lp->getStartDate();
                            $copyStart->setDate($startCopy->format('Y'), $startCopy->format('m'), $startCopy->format('d'));
                            $copyEnd = $lp->getEndDate();
                            $copyEnd->setDate($startCopy->format('Y'), $startCopy->format('m'), $startCopy->format('d'));
                            break;
                        case 2://tue
                            $copyStart = $lp->getStartDate();
                            $copyStart->setDate($startCopy->format('Y'), $startCopy->format('m'), $startCopy->format('d'));
                            $copyEnd = $lp->getEndDate();
                            $copyEnd->setDate($startCopy->format('Y'), $startCopy->format('m'), $startCopy->format('d'));
                            $copyStart->modify("+1 day");
                            $copyEnd->modify("+1 day");
                            break;
                        case 3://wed
                            $copyStart = $lp->getStartDate();
                            $copyStart->setDate($startCopy->format('Y'), $startCopy->format('m'), $startCopy->format('d'));
                            $copyEnd = $lp->getEndDate();
                            $copyEnd->setDate($startCopy->format('Y'), $startCopy->format('m'), $startCopy->format('d'));
                            $copyStart->modify("+2 day");
                            $copyEnd->modify("+2 day");
                            break;
                        case 4://thu
                            $copyStart = $lp->getStartDate();
                            $copyStart->setDate($startCopy->format('Y'), $startCopy->format('m'), $startCopy->format('d'));
                            $copyEnd = $lp->getEndDate();
                            $copyEnd->setDate($startCopy->format('Y'), $startCopy->format('m'), $startCopy->format('d'));
                            $copyStart->modify("+3 day");
                            $copyEnd->modify("+3 day");
                            break;
                        case 5://fri
                            $copyStart = $lp->getStartDate();
                            $copyStart->setDate($startCopy->format('Y'), $startCopy->format('m'), $startCopy->format('d'));
                            $copyEnd = $lp->getEndDate();
                            $copyEnd->setDate($startCopy->format('Y'), $startCopy->format('m'), $startCopy->format('d'));
                            $copyStart->modify("+4 day");
                            $copyEnd->modify("+4 day");
                            break;
                        case 6://sat
                            $copyStart = $lp->getStartDate();
                            $copyStart->setDate($startCopy->format('Y'), $startCopy->format('m'), $startCopy->format('d'));
                            $copyEnd = $lp->getEndDate();
                            $copyEnd->setDate($startCopy->format('Y'), $startCopy->format('m'), $startCopy->format('d'));
                            $copyStart->modify("+5 day");
                            $copyEnd->modify("+5 day");
                            break;
                        default://sun
                            $copyStart = $lp->getStartDate();
                            $copyStart->setDate($startCopy->format('Y'), $startCopy->format('m'), $startCopy->format('d'));
                            $copyEnd = $lp->getEndDate();
                            $copyEnd->setDate($startCopy->format('Y'), $startCopy->format('m'), $startCopy->format('d'));
                            $copyStart->modify("-1 day");
                            $copyEnd->modify("-1 day");
                            break;
                    }
                    //return response()->json($copyStart->format('d')-1);
                    //return response()->json($copyStart);
                    $copy->startDate = $copyStart;
                    $copy->endDate = $copyEnd;
                    $copy->save();
                    $copy->dozent()->sync($lp->dozent->pluck("id"));
                    $copy->klassen()->sync($lp->klassen->pluck("id"));
                    $copy->raum()->sync($lp->raum->pluck("id"));
                    if($copy->isManualStudents)
                        $copy->students()->sync($lp->students->pluck("id"));
                    foreach ($lp->merkmale as $merkmal)
                    {
                        $copy->setMerkmal($merkmal->key,$merkmal->value);
                    }
                    //return response()->json($copyStart);

                    //return response()->json($lessonPlan);

                }
                //return response()->json($lessonPlan);
            }
        }

        $endCopy = Carbon::createFromTimestamp($request->input("start"))->toDateTime();
        $endCopy->modify("+6 days");


        return $this->createJsonResponse("ok", false, 200);
    }

    public function deleteWeek(Request $request)
    {
        $start =  Carbon::createFromTimestamp($request->input("start"))->toDateTime();
        $end =  Carbon::createFromTimestamp($request->input("end"))->toDateTime();

        $id = Klasse::findOrFail($request->input("course_id"))->id;
        $entwurf = $request->get("draft_id");
        $entwurf = SchuljahrEntwurf::findOrFail($entwurf);
        $events = UnterrichtController::getUnterrichtInternalWithoutFormat($start, $end, $request->input("type", "schoolclass"), $id, $entwurf);

        foreach ($events as $event) {
            if (UnterrichtController::getLessonTyp($event) === "lessonPlan") {
                $lp = LessonPlan::findOrFail($event->id);

                if ($lp != null) {
                    DB::table('klasse_lesson_plan')->where([
                        'lesson_plan_id' => $lp->id,
                    ])->delete();
                    $merkmale = $lp->merkmale;
                    foreach ($merkmale as $merkmal) {
                        $merkmal->delete();
                    }

                    $lp->delete();
                }

            }
        }
        $events = UnterrichtController::getUnterrichtInternal($start, $end, "schoolclass", $id, $entwurf);

        return $this->createJsonResponse("ok", false, 200);
    }

    public function copySingleEvent(Request $request)
    {

        $klasse_id = Klasse::findOrFail($request->input("klasse_id"))->id;
        preg_match('~_(.*?)_~', $request->input("unique_id"), $output);//$output[1] contains lessonplan id

    }

    public function copyToClass(Request $request)
    {

        $klasse_id = Klasse::findOrFail($request->input("copy_klasse_id"))->id;
        preg_match('~_(.*?)_~', $request->input("unique_id"), $output);//$output[1] contains lessonplan id
        $lp = LessonPlan::findOrFail($output[1]);

        $copy = $lp->replicate();

        $copy->save();
        DB::table('klasse_lesson_plan')->insert([
            'klasse_id' => $klasse_id,
            'lesson_plan_id' => $copy->id,
        ]);
        return response()->json($copy);
    }
}

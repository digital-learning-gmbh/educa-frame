<?php

namespace App\Http\Controllers\API;

use App\BookingSystemCacheKeyValue;
use App\ExamExecutionDate;
use App\Ferienkalender;
use App\Http\Controllers\API\External\SVS\SVSController;
use App\Http\Controllers\AppController;
use App\Http\Controllers\Controller;
use App\Http\LessonProvider\LessonCalendar;
use App\Http\LessonProvider\LessonRegistry;
use App\Klasse;
use App\KlassenbuchEintrag;
use App\Lehrer;
use App\Lesson;
use App\PraxisBesuchstermin;
use App\Raum;
use App\Schuler;
use App\SchuljahrEntwurf;
use App\LessonPlan;
use App\Timeslot;
use Carbon\Carbon;
use Carbon\Traits\Creator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Mexitek\PHPColors\Color;

class UnterrichtController extends ApiController
{
    public function getExternalUnterricht(Request $request)
    {
        if($request->input("api_key") != "SFDsfdSFRrdghd454gfs43sa")
        {
            return response()->json(["status" => 0, "message" => "Ungültiger Zugriff"]);
        }
        $start = new \DateTime($request->input("start"));
        $end = new \DateTime($request->input("end"));
        $type = strtolower($request->get("type"));
        $foreign_id = $request->get("foreign_id");

        // recalculate the lesson
        $id = null;
        if($type == "teacher") {
            $svsnumber = BookingSystemCacheKeyValue::where('name', '=', 'svs.lehrer')->where('foreign_id', '=', $foreign_id)->first();
            if ($svsnumber == null)
                return response()->json(["status" => 0, "message" => "Der Dozent wurde nicht gefunden"]);
            $lehrer = Lehrer::where('external_booking_id','=',$svsnumber->foreign_id)->first();
            if($lehrer == null)
                return response()->json(["status" => 0, "message" => "Der Dozent ist noch nicht verlinkt"]);
            $id = $lehrer->id;
        } else if($type == "room")
        {
            $svsnumber = BookingSystemCacheKeyValue::where('name', '=', 'svs.raum')->where('foreign_id', '=', $foreign_id)->first();
            if ($svsnumber == null)
                return response()->json(["status" => 0, "message" => "Der Raum wurde nicht gefunden"]);
            $raum = Raum::where('external_booking_id','=',$svsnumber->foreign_id)->first();
            if($raum == null)
                return response()->json(["status" => 0, "message" => "Der Dozent ist noch nicht verlinkt"]);
            $id = $raum->id;
        } else
        {
            return response()->json(["status" => 0, "message" => "Ungültiger Typ oder Objekt nicht gefunden"]);
        }

        $result = self::getUnterrichtWithoutEntwurf($start,$end,$id,$type, false);
        return response()->json($result);
    }

    public function getUnterricht(Request $request)
    {
        $start = new \DateTime($request->input("start"));
        $end = new \DateTime($request->input("end"));
        $type = strtolower($request->get("type"));
        $id = $request->get("id");
        // if we have a fixed entwurf, everthing is fine.
        if ($request->has("entwurf_id")) {
            $entwurf = $request->get("entwurf_id");
            $entwurf = SchuljahrEntwurf::findOrFail($entwurf);

            $result = self::getUnterrichtInternal($start, $end, $type, $id, $entwurf, $request->input("breaks", false));
            return response()->json($result);
        }
        $result = self::getUnterrichtWithoutEntwurf($start,$end,$id,$type, $request->input("breaks", false));
        return response()->json($result);
    }


    public static function getUnterrichtWithoutEntwurf($start, $end, $id, $type, $breaks = false, $vactaion = true, $external_booking = false, $planning_state = "plan", $studyDays = true, $withExamDates = true)
    {
        // we dont have a fixed entwurf id..
        if ($type == "teacher") {
            $teacher = Lehrer::find($id);
            // teacher can be an x schools ! so we have to merge
            $globalResult = [];
            foreach ($teacher->schulen as $schule)
            {
                if($schule->getCurrentSchoolYear() == null)
                    continue;

                $finalEntwurf = $schule->getCurrentSchoolYear()->entwurf_id;
                if ($finalEntwurf == null) {
                    continue;
                }
                // as always
                $entwurf = SchuljahrEntwurf::findOrFail($finalEntwurf);
                $result = self::getUnterrichtInternal($start, $end, $type, $id, $entwurf, $breaks, $vactaion, $external_booking, "edit", $withExamDates);
                $globalResult = array_merge($globalResult, $result);
            }
            return UnterrichtController::removeDuplicatesFromArray($globalResult);
        } else if ($type == "room") {

            $raum = Raum::find($id);
            $primarySchool = $raum->schulen->first();
            if($primarySchool == null)
            {
                return [];
            }
            if($primarySchool->getCurrentSchoolYear() == null)
            {
                return [];
            }
            $finalEntwurf = $primarySchool->getCurrentSchoolYear()->entwurf_id;
            if ($finalEntwurf == null) {
                return [];
            }
            // as always
            $entwurf = SchuljahrEntwurf::findOrFail($finalEntwurf);
            $result = self::getUnterrichtInternal($start, $end, $type, $id, $entwurf, $breaks, $vactaion, $external_booking, $planning_state);
            return $result;
        } else if ($type == "schoolclass") {

            $schoolClass = Klasse::find($id);
            $finalEntwurf = $schoolClass->schuljahr->entwurf_id;
            if ($finalEntwurf == null) {
                return [];
            }
            // as always
            $entwurf = SchuljahrEntwurf::findOrFail($finalEntwurf);
            $result = self::getUnterrichtInternal($start, $end, $type, $id, $entwurf, $breaks, $vactaion, $external_booking, $planning_state, $studyDays, $withExamDates);
            return $result;
        } else if ($type == "student") {
            $eventsAll = [];
            $studentInstance = Schuler::find($id);
            if($studentInstance != null)
            {
                foreach ($studentInstance->klassenNurZukunft() as $schoolClass)
                {
                    foreach($schoolClass->cluster as $klasse)
                    {
                        $events = self::getUnterrichtWithoutEntwurf($start, $end, $klasse->id, "schoolclass", $breaks, $vactaion, $external_booking, "release", false, false);
                        $eventsAll = array_merge($eventsAll, $events);
                    }
                    $events = self::getUnterrichtWithoutEntwurf($start, $end, $schoolClass->id, "schoolclass", $breaks, $vactaion, $external_booking, "release", false, false);
                    $eventsAll = array_merge($eventsAll, $events);


                    $examDates = ExamExecutionDate::whereIn("modul_exam_execution_id", DB::table("modul_exam_execution_schuler")->where("schuler_id","=",$studentInstance->id)->pluck("modul_exam_execution_id")->toArray())
                        ->where(function ($query) {
                            $query->where("status", "=", "student")->orWhere("status","=","public");
                        })->with("rooms")->with("teacher")->get();
                    foreach ($examDates as $examDate) {
                        $eventsAll[] = self::formatEvent($examDate, $id);
                    }
                }
            }
            return UnterrichtController::removeDuplicatesFromArray($eventsAll);
        }
        return [];
    }

    public static function removeDuplicatesFromArray($array)
    {
        $keys = [];
        $result = [];
        foreach ($array as $item)
        {
            if(!array_key_exists("unique_id", $item) || !in_array($item["unique_id"], $keys))
            {
                $result[] = $item;
                if(array_key_exists("unique_id", $item))
                {
                    $keys[] = $item["unique_id"];
                }
            }
        }
        return $result;
    }

    public static function getUnterrichtInternalWithoutFormat($start, $end, $type, $id, $entwurf, $planning_state = "plan", $withExamDates = true)
    {

        //$result = [];
        $events = [];
        if ($type == "teacher") {
            $events = LessonCalendar::getCalendarTeacher($start, $end, $id, $entwurf->id, $planning_state, $withExamDates);
        } else if ($type == "room") {
            $events = LessonCalendar::getCalendarRoom($start, $end, $id, $entwurf->id, $planning_state, $withExamDates);
        } else if ($type == "schoolclass") {
            $events = LessonCalendar::getCalendarSchoolClass($start, $end, $id, $entwurf->id, $planning_state, $withExamDates);
        } else if($type == "student") {
            $events = LessonCalendar::getCalendarForStudent($start, $end, $id, $entwurf->id, $planning_state, $withExamDates);
        }

        return $events;
    }

    public static function getUnterrichtInternal($start, $end, $type, $id, $entwurf, $breaks = false, $vactaion = true, $withExternalBooking = false, $planning_state = "plan", $studydays = true, $withExamDates = true)
    {
        $result = [];
        $ferienKalendar = null;
        if($vactaion && $entwurf != null)
        {
            // Ferientermine
            $schule = $entwurf->schuljahr->schule;
            $ferienKalendar = Ferienkalender::find($schule->getEinstellungen("calendar_id", ""));
            if($ferienKalendar != null)
            {
                foreach ($ferienKalendar->ferienzeits as $ferienzeit)
                {
                    $event = [];
                    $event["unique_id"]  = "vacation_".$ferienzeit->id;
                    $event["editable"] = false;
                    $event["display"] = "";
                    $event["title"] = $ferienzeit->name;
                    $event["color"] = "#DCDCDC";
                    $event["start"] = Carbon::parse($ferienzeit->start)->format(\DateTime::ISO8601);
                    $event["end"] = Carbon::parse($ferienzeit->end)->format(\DateTime::ISO8601);
                    $event["type"] = "vacation";
                    $event["eventTextColor"] = "#fff";
                    try {
                        $color = new Color($event["color"]);
                        if($color->isLight())
                        {
                            $event["eventTextColor"] = "#000";
                        }
                    } catch (\Exception $exception)
                    {
                        //
                    }
                    $result[] = $event;
                }
            }



        }

        if($type == "schoolclass" && $studydays == true && Klasse::find($id)) {
            $klasse = Klasse::find($id);

            $endeIterator = new Carbon($end);
            $iterator = new Carbon($start);
            $iterator = $iterator->startOfDay();
            while ($iterator->isBefore($endeIterator)) {
                if ($iterator->dayOfWeek ==  1 && ($klasse->daysOfWork == "tu_we" || $klasse->daysOfWork == "we_th" || $klasse->daysOfWork == "th_fr")) {
                    // add all mondays as study days
                    $event = self::createStudientag($iterator);
                    $result[] = $event;
                }
                if ($iterator->dayOfWeek ==  2 && ($klasse->daysOfWork == "we_th" || $klasse->daysOfWork == "th_fr")) {
                    // add all mondays as study days
                    $event = self::createStudientag($iterator);
                    $result[] = $event;
                }

                if ($iterator->dayOfWeek ==  3 && ($klasse->daysOfWork == "mo_tu" || $klasse->daysOfWork == "th_fr")) {
                    // add all mondays as study days
                    $event = self::createStudientag($iterator);
                    $result[] = $event;
                }

                if ($iterator->dayOfWeek ==  4 && ($klasse->daysOfWork == "mo_tu" || $klasse->daysOfWork == "tu_we" )) {
                    // add all mondays as study days
                    $event = self::createStudientag($iterator);
                    $result[] = $event;
                }

                if ($iterator->dayOfWeek ==  5 && ($klasse->daysOfWork == "mo_tu" || $klasse->daysOfWork == "tu_we" || $klasse->daysOfWork == "we_th" )) {
                    // add all mondays as study days
                    $event = self::createStudientag($iterator);
                    $result[] = $event;
                }

                $iterator->addDay();
            }
        }

        $events = self::getUnterrichtInternalWithoutFormat($start, $end, $type, $id, $entwurf, $planning_state, $withExamDates);

        foreach ($events as $event) {
            $result[] = self::formatEvent($event, $id);
        }

        // generate breaks or special events rein
        if($type == "schoolclass" && $breaks == true)
        {
            $endeIterator = new Carbon($end);
            // load breaks and stuff
            $timeslots = $entwurf->schuljahr->getTimeslots;
            foreach ($timeslots as $timeslot)
            {
                $time = explode(":",$timeslot->begin);
                $time2 = explode(":",$timeslot->end);
                $iterator = new Carbon($start);
                while ($iterator->isBefore($endeIterator))
                {
                    $event = [];
                    $event["editable"] = false;
                    $event["display"] = "background";
                    $event["title"] = $timeslot->displayText;
                    $event["color"] = $timeslot->color;
                    $iterator->setHour($time[0]);
                    $iterator->setMinute($time[1]);
                    $event["start"] = $iterator->format(\DateTime::ISO8601);
                    $iterator->setHour($time2[0]);
                    $iterator->setMinute($time2[1]);
                    $event["end"] = $iterator->format(\DateTime::ISO8601);
                    $event["eventTextColor"] = "#fff";
                    try {
                        $color = new Color($event["color"]);
                        if($color->isLight())
                        {
                            $event["eventTextColor"] = "#000";
                        }
                    } catch (\Exception $exception)
                    {
                        //
                    }
                    $result[] = $event;
                    // Reset
                    $iterator->setMinute(0);
                    $iterator->setHour(0);
                    $iterator->addDay();
                }
            }
        }


        try {
            if ($type == "teacher" && $withExternalBooking == true) {
                $lehrer = Lehrer::find($id);
                if ($lehrer != null && $lehrer->external_booking_id != null) {
                    $startIterator = new Carbon($start);
                    while ($startIterator->isBefore($end)) {
                        $externalTeaching = SVSController::getStundenplanLeh($startIterator->weekOfYear, $startIterator->year, $lehrer->external_booking_id);
                        // print_r($externalTeaching);
                        if (array_key_exists("id", $externalTeaching["belegung"])) {
                            $result[] = self::formatSVSEvent($externalTeaching["belegung"], $id);
                        } else {
                            foreach ($externalTeaching["belegung"] as $belegung) {
                                $result[] = self::formatSVSEvent($belegung, $id);
                            }
                        }

                        $startIterator->addWeek();
                    }
                }
            }

            if ($type == "room" && $withExternalBooking == true) {
                $raum = Raum::find($id);
                if ($raum != null && $raum->external_booking_id != null) {
                    $startIterator = new Carbon($start);
                    while ($startIterator->isBefore($end)) {
                        $externalTeaching = SVSController::getStundenplanRoom($startIterator->weekOfYear, $startIterator->year, $raum->external_booking_id);
                        // print_r($externalTeaching);
                        if (array_key_exists("id", $externalTeaching["belegung"])) {
                            $result[] = self::formatSVSEvent($externalTeaching["belegung"], $id);
                        } else {
                            foreach ($externalTeaching["belegung"] as $belegung) {
                                $result[] = self::formatSVSEvent($belegung, $id);
                            }
                        }

                        $startIterator->addWeek();
                    }
                }
            }

        } catch (\Exception $exception)
        {
            Log::error("Generating Timetable from external service: ". $exception->getTraceAsString());
        }

        return $result;
    }

    private static function createStudientag($iterator)
    {
        $event = [];
        $event["editable"] = false;
        $event["display"] = "background";
        $event["title"] = "Kein Studientag";
        $event["color"] = "#000";
        $event["start"] = $iterator->setHour(0)->setMinute(0)->format(\DateTime::ISO8601);
        $event["end"] = $iterator->setHour(23)->setMinute(59)->format(\DateTime::ISO8601);
        $event["type"] = "vacation";
        $event["eventTextColor"] = "#fff";
        try {
            $color = new Color($event["color"]);
            if($color->isLight())
            {
                $event["eventTextColor"] = "#000";
            }
        } catch (\Exception $exception)
        {
            //
        }
        return $event;
    }

    private static function formatSVSEvent($eventSVS,$ressourceId)
    {
        $event = [];
        $event["editable"] = false;
        $event["display"] = "vacation";
        $event["resourceId"] = $ressourceId;
        $event["color"]  = "#A4C7EA";
        $event["type"] = "external_booking";
        $event["title"] = "SVS belegt: ".self::safeArrayKeyExits($eventSVS,"fachname").", ".self::safeArrayKeyExits($eventSVS,"kursname").", ".self::safeArrayKeyExits($eventSVS,"raumname");
        $start = Carbon::parse($eventSVS["start"]);
        $event["start"] = $start->format(\DateTime::ISO8601);
        $ende = Carbon::parse($eventSVS["ende"]);
        $event["end"] = $ende->format(\DateTime::ISO8601);
        $event["eventTextColor"] = "#000";
        try {
            $color = new Color($event["color"]);
            if($color->isLight())
            {
                $event["eventTextColor"] = "#000";
            }
        } catch (\Exception $exception)
        {
            //
        }
        return $event;
    }

    private static function safeArrayKeyExits($array, $key)
    {
        if(array_key_exists($key, $array))
        {
            if(!is_array($array[$key]))
                return $array[$key];
            return implode(",",$array[$key]);
        }
        return "";
    }

    public static function formatEvent($event, $ressourceId) {
        $frontendEvent = [];
        $frontendEvent["start"] = $event->getStartDate()->format(\DateTime::ISO8601);
        $frontendEvent["end"] = $event->getEndDate()->format(\DateTime::ISO8601);
        $frontendEvent["unique_id"] = UnterrichtController::getUniqueIDForLesson($event);
        $frontendEvent["resourceId"] = $ressourceId;

        if($event->fach_id == null)
        {
            $frontendEvent["fach_id"] = -1;
        } else {
            $frontendEvent["fach_id"] = $event->fach_id;
        }

        if (str_contains($event->getId(), "section")) {
            // Lernabschnitt
            $frontendEvent["title"] = $event->name;
            $frontendEvent["type"] = UnterrichtController::getLessonTyp($event);
            $frontendEvent["editable"] = false;
            $frontendEvent["allDay"] = true;
            $frontendEvent["classNames"] = [$event->type == "praxis" ? "bg-primary" : "bg-secondary"];
        }
        elseif ($event instanceof LessonPlan){//lessonplan recurrent
            $frontendEvent["id"] = $event->getId();
            $frontendEvent["fach_abk"] = $event->fachAbk();
            $frontendEvent["title"] = $event->fachname();
            if($event->fach != null)
            {
                $frontendEvent["color"] = $event->fach->color;
                $frontendEvent["eventTextColor"] = "#fff";
                try {
                    $color = new Color($frontendEvent["color"]);
                    if($color->isLight())
                    {
                        $frontendEvent["eventTextColor"] = "#000";
                    }
                } catch (\Exception $exception)
                {
                    //
                }
            }
            $frontendEvent["raum_id"] = $event->raum->pluck('id');
            $frontendEvent["lehrer_id"] = $event->dozent->pluck('id');

            $frontendEvent["dozent"] = $event->dozentname();
            $frontendEvent["raum"] = $event->raumname();
            $frontendEvent["plan_id"] = $event->id;
            $frontendEvent["description"] = $event->description;
            $frontendEvent["subtitle"] = $event->subtitle;
            if($frontendEvent["subtitle"] == "")
                $frontendEvent["subtitle"] = $event->fachAbk();
            if($frontendEvent["title"] == "Kein Fach" && $event->subtitle != null)
            {
                $frontendEvent["title"] = $event->subtitle;
                $frontendEvent["subtitle"] = "";
            }
            $frontendEvent["bookId"] = $event->getStartDate()->format("Y")."_".$event->id."_".$event->getStartDate()->format("W");
            $frontendEvent["type"] = UnterrichtController::getLessonTyp($event);
            $frontendEvent["klassen_id"] = $event->klassen->pluck('id');
            $frontendEvent["klassen_name"] = $event->klassen->pluck('name');
            $merkmale = [];
            foreach ($event->merkmale as $merkmal)
            {
                $merkmale[$merkmal->key] = $merkmal->value;
            }
            $frontendEvent["merkmal"] = $merkmale;

            $klassenbuch = KlassenbuchEintrag::where('lesson_id', '=', $frontendEvent["unique_id"])->first();
            if($klassenbuch != null)
            {
                $frontendEvent["klassenbuch"] = $klassenbuch;
            } else {
                $frontendEvent["klassenbuch"] = -1;
            }
        } elseif ($event instanceof PraxisBesuchstermin) {//lessonplan recurrent

            $frontendEvent["type"] = UnterrichtController::getLessonTyp($event);
            $frontendEvent["title"] = $event->name;
            $frontendEvent["dozent"] = $event->einsatz->schuler->displayName;
            $frontendEvent["raum"] = "Kein Unternehmen";
            $frontendEvent["color"] = "#ADFF2F";
            $frontendEvent["eventTextColor"] = "#fff";
            try {
                $color = new Color($frontendEvent["color"]);
                if($color->isLight())
                {
                    $frontendEvent["eventTextColor"] = "#000";
                }
            } catch (\Exception $exception)
            {
                //
            }
            $frontendEvent["editable"] = false;
            if($event->einsatz->unternehmen != null)
            {
                $frontendEvent["raum"] = $event->einsatz->unternehmen->name;
            }
        } elseif ($event instanceof Lesson) {//lesson ausnahme
            $frontendEvent["id"] = $event->getId();
            $frontendEvent["bookId"] = $event->getStartDate()->format("Y")."_".$event->getParentId()."_".$event->getStartDate()->format("W")."_".$event->getId();
            $frontendEvent["title"] = $event->fachAbk();
            $frontendEvent["fach_abk"] = $event->fachAbk();
            $frontendEvent["raum_id"] = $event->raum->pluck('id');
            $frontendEvent["lehrer_id"] = $event->dozent->pluck('id');
            $frontendEvent["dozent"] = $event->dozentname();
            $frontendEvent["raum"] = $event->raumname();
            $frontendEvent["plan_id"] = $event->getParentId();
            $frontendEvent["subtitle"] = $event->subtitle;
            if($frontendEvent["title"] == "Kein Fach" && $event->subtitle != null)
            {
                $frontendEvent["title"] = $event->subtitle;
                $frontendEvent["subtitle"] = "";
            }
            $frontendEvent["klassen_id"] = $event->klassen->pluck('id');
            $frontendEvent["klassen_name"] = $event->klassen->pluck('name');
            $frontendEvent["type"] = UnterrichtController::getLessonTyp($event);
            $frontendEvent["editable"] = false;
            if($event->fach != null)
            {
                $frontendEvent["color"] = $event->fach->color;
                $frontendEvent["eventTextColor"] = "#fff";
                try {
                    $color = new Color($frontendEvent["color"]);
                    if($color->isLight())
                    {
                        $frontendEvent["eventTextColor"] = "#000";
                    }
                } catch (\Exception $exception)
                {
                    //
                }
            }
            // depcreated
            if($event->type == 'single'){
                $frontendEvent["borderColor"] = 'greenyellow';
                $frontendEvent["lessonType"] = 'single';
            // end depreacfet
            } else if($event->type == 'ausfall'){
                $frontendEvent["borderColor"] = 'red';
                $frontendEvent["lessonType"] = 'ausfall';
            } else {
                $frontendEvent["lessonType"] = 'vertretung';
                $frontendEvent["borderColor"] = 'yellow';
            }

            $merkmale = [];
            foreach ($event->merkmale as $merkmal)
            {
                $merkmale[$merkmal->key] = $merkmal->value;
            }
            $frontendEvent["merkmal"] = $merkmale;

            $klassenbuch = KlassenbuchEintrag::where('lesson_id', '=', $frontendEvent["unique_id"])->first();
            if($klassenbuch != null)
            {
                $frontendEvent["klassenbuch"] = $klassenbuch;
            } else {
            $frontendEvent["klassenbuch"] = -1;
            }
        } elseif ($event instanceof ExamExecutionDate)
        {
            $examParts = $event->examParts;
            $subjects = [];
            foreach ($examParts as $examPart)
            {
                foreach ($examPart->subjects as $subject)
                {
                    $subjects[] = $subject->name;
                }
            }
            $preTitle = "Prüfung: ";
            if($event->examExecution->type == "repeat_exam")
            {
                $preTitle = "Wiederholungsprüfung: ";
            }
            if($event->examExecution->type == "oral_exam")
            {
                $preTitle = "mündl. Prüfung: ";
            }
            $frontendEvent["title"] = $preTitle.join(", ",$subjects);
            $frontendEvent["type"] = UnterrichtController::getLessonTyp($event);
            $frontendEvent["editable"] = false;
            $frontendEvent["color"] = "#D50000";
            $frontendEvent["eventTextColor"] = "#fff";
            $frontendEvent["dozent"] = $event->dozentname();
            $frontendEvent["raum"] = $event->raumname();
            $frontendEvent["allDay"] = $event->getStartDate()->format("H:i") == "00:00";
        }

        return $frontendEvent;
    }

    /**
     * Erstellt zu jedem Event eine eindeutige ID
     * z.B. single_223232_23_01_2020
     *      lessonplan_234234234_23_01_2020
     * @param $event
     */
    public static function getUniqueIDForLesson($event)
    {
        return UnterrichtController::getLessonTyp($event)."_".$event->id."_".$event->getStartDate()->format("d_m_Y");
    }


    public static function getLessonTyp($event)
    {
        if(str_contains($event->getId(), "section"))
            return "section";
        if($event instanceof LessonPlan)
            return "lessonPlan";
        if($event instanceof Timeslot)
            return "timeslot";
        if($event instanceof Lesson)
            return "lesson";
        if($event instanceof PraxisBesuchstermin)
            return "praxisBesuch";
        if($event instanceof ExamExecutionDate)
            return "examDates";

        return "other";
    }
}

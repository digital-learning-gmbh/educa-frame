<?php

namespace App\Http\Controllers\API\V1;

use App\Appointment;
use App\CloudID;
use App\Group;
use App\Http\AppointmentProvider\AppointmentCalender;
use App\Http\AppointmentProvider\AppointmentRegistry;
use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\API\UnterrichtController;
use App\Http\Controllers\API\V1\Groups\GroupController;
use App\Klasse;
use App\Lehrer;
use App\ModelMeeting;
use App\Models\OutlookShareToken;
use App\Models\SingleAppointment;
use App\Observers\FeedObserver;
use App\PermissionConstants;
use App\Schuler;
use App\Section;
use Carbon\Carbon;
use Eluceo\iCal\Component\Calendar;
use Eluceo\iCal\Component\Event;
use Eluceo\iCal\Domain\ValueObject\Date;
use Eluceo\iCal\Domain\ValueObject\SingleDay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Mexitek\PHPColors\Color;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Single;
use Spatie\Color\Hex;
use StuPla\CloudSDK\calendarful\Calendar\CalendarFactory;

class EventController extends ApiController
{
    /**
     * @OA\Post (
     *     tags={"v1","events"},
     *     path="/api/v1/events",
     *     description="Events for the global calendar",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="start",
     *       required=false,
     *       in="query",
     *       description="start timestamp",
     *         @OA\Schema(
     *           type="date"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="end",
     *       required=false,
     *       in="query",
     *       description="end timestamp",
     *         @OA\Schema(
     *           type="date"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="groups",
     *       required=false,
     *       in="query",
     *       description="ids of groups which events should be returned, if this parameter is not used, all events are returned of the groups of the current user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="direct",
     *       required=false,
     *       in="query",
     *       description="default: false, direct innvations to events",
     *         @OA\Schema(
     *           type="boolean"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="payload",
     *       required=false,
     *       in="query",
     *       description="default: true, removes the handler for fullcalendar",
     *         @OA\Schema(
     *           type="boolean"
     *         )
     *     ),
     *     @OA\Response(response="200", description="")
     * )
     */
    public function events(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);

        // override
        if($request->has("viewForCloudId") && CloudID::find($request->viewForCloudId))
        {
            $cloud_user = CloudID::find($request->viewForCloudId);
        }

        if(!$request->input("start") || !$request->input("end"))
            return $this->createJsonResponse("Start and/or Enddate not defined.", true, 400);

        $start = Carbon::createFromTimestamp($request->input("start"));
        $end = Carbon::createFromTimestamp($request->input("end"));
        $events = [];

        // get events for sections directly
        if ($request->has("sections")) {
            $sectionIds = $request->input("sections");
        } else {
            if (!$request->has("groups")) {
                $groups = $cloud_user->gruppen()->pluck("id"); // all group Ids
            } else {
                $groups = $request->input("groups", "");
            }
            // Load events from range
            $sectionIds = Section::whereIn('group_id', $groups)->pluck('id');
        }

        if($request->input("showFerien",true)) {
            $groups = Group::whereIn("id",Section::whereIn('id', $sectionIds)->pluck('group_id'))->get();
        $klassen = [];
        foreach($groups as $group)
        {
            if(str_contains($group->external_identifier,"schoolclass_"))
            {
                $klassen[] = Klasse::find(str_replace("schoolclass_","",$group->external_identifier));
            }
        }

        $ferienCalendar = [];
        foreach($klassen as $klasse)
        {
            if($klasse->kalender_id != null)
            {
                $ferienCalendar[] = $klasse->kalender_id;
            } else
            {
                $schule = $klasse->schuljahr->schule;
                $ferienCalendar[] = $schule->getEinstellungen("calendar_id", "");
            }
        }
//        foreach(Ferienzeit::whereIn("ferienkalender_id",$ferienCalendar)->get() as $ferien)
//        {
//            $events[] = UnterrichtController::formatVactaion($ferien);
//        }
    }

        $appoints = $this->loadEventsForRange($start, $end, $cloud_user, $sectionIds,
            $request->input("direct"), null,null, !$request->input("showRemovedEvents"), $request->input("eventTypeFilter"));
        foreach ($appoints as $appoint)
        {
            $formattedEvent = $this->formatEvent($appoint, $cloud_user);
            if($formattedEvent != null)
            {
                $events[] = $formattedEvent;
            }
        }

        // Teaching
        if($request->input("dozent","false") == "true")
        {
           $controler = new UnterrichtController();
           $unterricht = $controler->getUnterrichtWithoutEntwurf($start->toDateTimeString(),$end->toDateTimeString(),$cloud_user->getAppLogin("klassenbuch"), "teacher");
           $events = array_merge($events,$unterricht);
        }

        // Response
        if($request->has('payload') && $request->input("payload"))
        {
            return response()->json($events);
        }

        $outlookShareToken = OutlookShareToken::where("cloud_id","=",$cloud_user->id)->first();
        return $this->createJsonResponse("ok", false,200, [ "events" => $events, "outlookShareToken" => $outlookShareToken]);
    }

    public function eventsOutlook(Request $request)
    {

        $shareToken = OutlookShareToken::where(["token" => $request->input("token")])->first();
        $cloud_user = $shareToken?->cloudId;
        $filters = $shareToken?->filters??[];

        if( !$shareToken || !$cloud_user )
            return $this->createJsonResponse("not found", true, 400);

        $groupIdFilter = array_key_exists("groupIds", $filters)? $filters["groupIds"] : null;
        $directFilter = array_key_exists("direct", $filters)? !!$filters["direct"] : null;
        $showRemovedEventsFilter = array_key_exists("showRemovedEvents", $filters)? !!$filters["showRemovedEvents"] : null;
        $eventTypeFilter = array_key_exists("eventTypeFilter", $filters)? $filters["eventTypeFilter"] : null;

        $groups = $cloud_user->groups();
        if(is_array($groupIdFilter))
            $groups->whereIn("group_id",$groupIdFilter);
        $groups = $groups->get()->pluck("id");
        $sectionIds = Section::whereIn('group_id', $groups)->pluck('id');

        $start = Carbon::now()->subYears(2);
        $end = Carbon::now()->addYears(2);

        $appoints = $this->loadEventsForRange($start, $end, $cloud_user, $sectionIds, $directFilter, null,null, !$showRemovedEventsFilter, $eventTypeFilter);

        $calendar = new Calendar("educa_".$cloud_user->id);
        $calendar->setName("educa");
        foreach ($appoints as $appoint) {
            $eventsFormated = $this->formatEvent($appoint, $cloud_user);
            if($eventsFormated == null)
                continue;

            $event = new Event();
            $event
                ->setDtStart(new \DateTime($eventsFormated["start"]))
                ->setDtEnd(new \DateTime($eventsFormated["end"]))
                ->setUniqueId($eventsFormated["id"])
                ->setUrl($_SERVER['SERVER_NAME'] . "/app/calendar?event_id=" . $eventsFormated["id"])
                ->setSummary($eventsFormated["title"])
                ->setDescriptionHTML($appoint->description)
                ->setLocation($appoint->location)
                ->setStatus($appoint->state == 0 ? EVENT::STATUS_TENTATIVE : EVENT::STATUS_CONFIRMED);
            $calendar->addComponent($event);
        }

        $lessons = [];
        $lehrer = Lehrer::find($cloud_user->getAppLogin("klassenbuch"));
        if($lehrer != null)
        {
            $controler = new UnterrichtController();
            $unterricht = $controler->getUnterrichtWithoutEntwurf($start->toDateTime(),$end->toDateTime(),$lehrer->id, "teacher");
            $lessons = array_merge($lessons,$unterricht);
        }

        $student = Schuler::find($cloud_user->getAppLogin("student"));
        if($student != null)
        {
            $controler = new UnterrichtController();
            $unterricht = $controler->getUnterrichtWithoutEntwurf($start->toDateTime(),$end->toDateTime(),$student->id, "student");
            $lessons = array_merge($lessons,$unterricht);
        }

        // Stundenplan
        $usersTeacher = Lehrer::find($cloud_user->getAppLogin("klassenbuch"));
        $usersStudent = Schuler::find($cloud_user->getAppLogin("student"));

        $type = $usersTeacher?->id?  "teacher" : ($usersStudent?->id? "student" : null);
        $id = $usersTeacher?->id?? ($usersStudent?->id?? null);

        if( $type && $id)
        {
           $lessons = array_merge($lessons, UnterrichtController::getUnterrichtWithoutEntwurf($start, $end, $id, $type, false, true, false));
        }


        foreach ($lessons as $appoint) {
            if (array_key_exists("type", $appoint) && ($appoint["type"] == "lessonPlan" || $appoint["type"] == "lesson")) {
                $event = new Event();
                $event
                    ->setDtStart(Carbon::parse($appoint["start"])->toDateTime())
                    ->setDtEnd(Carbon::parse($appoint["end"])->toDateTime())
                    ->setUniqueId($appoint["unique_id"])
                    ->setUrl("https://educa-portal.de")
                    ->setSummary($appoint["title"])
                    ->setDescriptionHTML("Dozent:in: ".(array_key_exists("dozent",$appoint) ? $appoint["dozent"] : "" )." <br>".$appoint["subtitle"]." <br>".(array_key_exists("description",$appoint) ? $appoint["description"] : ""))
                    ->setLocation($appoint["raum"])
                    ->setStatus(EVENT::STATUS_CONFIRMED);
                $calendar->addComponent($event);
            } else {
                // ignore
            }


            return parent::createJsonResponse("timetable generated",false, 200,[ "events" => $result]);
        }

        // 4. Set HTTP headers
    //    header('Content-Type: text/calendar; charset=utf-8');
  //      header('Content-Disposition: attachment; filename="cal.ics"');

        // 5. Output
        echo $calendar->render();
    }

    public static function loadEventsForRange(Carbon $start, Carbon $end, $cloudUser, $sectionIds, $directInvation = true,
                                              $limit = null, $status = null, $removeExceptions = true,
                                              $eventTypeFilter = null)
    {
        $appoints = collect(AppointmentCalender::getCalendar($start->toDateTime(),$end->toDateTime(),new AppointmentRegistry($cloudUser, $sectionIds,$directInvation,$status),$limit));

        foreach ($appoints as $appoint) {
            $appoint->append('attendees');
            $appoint->append('organisators');
        }

        $appoints = $appoints->filter(function ($appoint) use ($eventTypeFilter, $removeExceptions)
        {
            if($appoint instanceof SingleAppointment)
            {
                if($appoint->exception_type == "remove" && $removeExceptions)
                {
                    return false;
                }
            }
            if($eventTypeFilter != null)
            {
                if($appoint->eventClass == "exam" && array_key_exists("examEventType", $eventTypeFilter) && $eventTypeFilter["examEventType"])
                {
                    return true;
                }
                if(($appoint->eventClass == "default" || $appoint instanceof SingleAppointment) && array_key_exists("defaultEventType", $eventTypeFilter) && $eventTypeFilter["defaultEventType"])
                {
                    return true;
                }
                return false;
            }
            return true;
        })->values();

        return $appoints;
    }

    public static function formatEvent($events, $cloud_user)
    {
        $frontendEvent = [];
        $frontendEvent["id"] = $events->id;
        $frontendEvent["title"] = $events->title;
        $frontendEvent["start"] = $events->getStartDate()->format(\DateTime::ISO8601);;
        $frontendEvent["end"] = $events->getEndDate()->format(\DateTime::ISO8601);;
        if($events->state == 0) {
            $color = Hex::fromString($events->color);
            $frontendEvent["color"] = (string) $color->toRgba(0.4);
        } else {
            $frontendEvent["color"] = $events->color;
        }
        $frontendEvent["editable"] = false;
        $frontendEvent["type"] = "appointment";
        $frontendEvent["eventClass"] = $events->eventClass;

        $frontendEvent["hasRepetition"] = !($events->recurrenceType == "none" || $events->recurrenceType == null);
        $frontendEvent["repetitionTurnus"] = $events->recurrenceTurnus;
        $frontendEvent["repetitionUntil"] = $events->recurrenceUntil;
        $frontendEvent["display"] = $events->display;
        if($events->display == "background")
        {
            $frontendEvent["color"] = "#b3b3b3";
        }
        $orga = $events->getOrganisatorsAttribute();
        if($orga != null && in_array($cloud_user->id,$orga->pluck("id")->toArray())) {
            $frontendEvent["editable"] = true;
            $frontendEvent["display"] = "auto";
        }

        if($events->eventClass == "exam" && $cloud_user->hasPermissionTo(PermissionConstants::EDUCA_CALENDAR_EDIT_ALL)) {
            $frontendEvent["editable"] = true;
            $groups = Group::whereIntegerInRaw("id",$events->sections()->pluck("group_id"))->get();
            foreach ($groups as $group)
            {
                $isBlocked = false;
                $identifier = $group->external_identifier;
                if($identifier && str_contains($identifier,"schoolclass")) {
                    $schoolclass = Klasse::find(str_replace("schoolclass_", "", $identifier));
                    $schule = $schoolclass->schuljahr->schule;
                    $isBlocked = $schule->getEinstellungen("yearplaner_blocked",false);
                }
                if($frontendEvent["editable"] && $isBlocked)
                {
                    $frontendEvent["editable"] = false;
                    break;
                }
            }
        }

        if($events->getStartDate()->format("H:i") == "00:00" && ($events->getEndDate()->format("H:i") == "00:00" || $events->getEndDate()->format("H:i") == "23:59"))
            $frontendEvent["allDay"] = true;

        $frontendEvent["exception_type"] = "none";
        if($events instanceof SingleAppointment) {
            $frontendEvent["type"] = "single-appointment";
            $frontendEvent["exception_type"] = $events->exception_type;
            $frontendEvent["appointment_id"] = $events->appointment_id;
            $frontendEvent["occurrence_date"] = $events->occurrenceDate;
            $mainAppointment = Appointment::find($events->appointment_id);
            $frontendEvent["appointment"] = self::formatEvent($mainAppointment,$cloud_user);
            $color = Hex::fromString($events->color);
            if ($events->exception_type == "remove") {
                $frontendEvent["title"] = "(Abgesagt) ".$frontendEvent["title"];
                $frontendEvent["color"] = (string)$color->toRgba(0.5);
            } else
            // here we have to check if we are in the single appoint, the other stuff would be save
            if(!$events->isTeilnehmerInvited($cloud_user->id))
            {
                return null;
            }
        }
        return $frontendEvent;
    }

    public function eventDetails($id, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        $appoints = Appointment::find($request->input("event_id"));
        $editable = false;
        $orga = $appoints->getOrganisatorsAttribute();
        if($orga != null && in_array($cloud_user->id,$orga->pluck("id")->toArray())) {
            $editable = true;
        }
        return response()->json([
            "event" => $appoints,
            "tags" => $appoints->tags(),
            "orga" => $appoints->getOrganisatorsAttribute(),
            "editable" => $editable
        ]);
    }

    /**
     * @OA\Post (
     *     tags={"v1","events"},
     *     path="/api/v1/events/create",
     *     description="Create a new event",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="start",
     *       required=false,
     *       in="query",
     *       description="start timestamp",
     *         @OA\Schema(
     *           type="date"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="end",
     *       required=false,
     *       in="query",
     *       description="end timestamp",
     *         @OA\Schema(
     *           type="date"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="title",
     *       required=false,
     *       in="query",
     *       description="title of the event",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="location",
     *       required=false,
     *       in="query",
     *       description="location of the event",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="description",
     *       required=false,
     *       in="query",
     *       description="description of the event",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="color",
     *       required=false,
     *       in="query",
     *       description="color of the event",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="attendees",
     *       required=true,
     *       in="query",
     *       description="array with cloud ids that are attendees of the event",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="organisators",
     *       required=true,
     *       in="query",
     *       description="array with cloud ids that are organisators of the event, the current user will automatically the organisator and it is not required to add them to this array",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="sections",
     *       required=true,
     *       in="query",
     *       description="array with sections ids that are attendees of the event",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Returns the created event")
     * )
     */
    public function createEvent(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);

        if($request->input("start") && $request->input("end"))
        {
            if ( Carbon::parse($request->input("start"))->isAfter($request->input("end")) )
                return $this->createJsonResponse("Startdate is after Enddate", true, 400);
        }

        $appointment = new Appointment;
        $appointment->title = $request->input("title");
        $appointment->startDate = date("Y-m-d H:i",strtotime($request->input("start")));
        $appointment->endDate = date("Y-m-d H:i",strtotime($request->input("end")));
        $appointment->location = $request->input("location");
        $appointment->description = $request->input("description");
        $appointment->color = $request->input("color","#3490dc");
        $appointment->remember_minutes = $request->input("remember_minutes", -1);
        $appointment->display = $request->input("display","auto");
        $appointment->setEventClass($request->input("eventClass","default"));

        $appointment->save();

        $appointment->rooms()->sync($request->input("rooms"));

        $teilnehmers = $request->input("attendees",[]);
        $orga = $request->input("organisators",[]);
        if($teilnehmers)
        foreach ($teilnehmers as $teilnehmer) {
            if (!in_array($teilnehmer, $orga)) {
                $tn = CloudID::find($teilnehmer);
                if($tn == null)
                    continue;
                $can_discard_invites = $tn->hasPermissionTo(PermissionConstants::EDUCA_CALENDAR_CAN_DISCARD_INVITES) ? 0 : 1;
                if($appointment->display == "background")
                {
                    $can_discard_invites = 1;
                }
                $appointment->addTeilnehmerById($teilnehmer, $can_discard_invites);
            }
        }

        $orga = $request->input("organisators");
        if($orga)
            foreach ($orga as $teilnehmer) {
                $appointment->addTeilnehmerById($teilnehmer, 0,100);
            }

        $gruppes =$request->input("sections");
        if($gruppes)
            foreach ($gruppes as $gruppe)
        {
                $appointment->addSectionById($gruppe);
        }

        // always add the user
        $appointment->addTeilnehmerById($cloud_user->id, 1, 100);

        // $appointment->syncTags($request->input("tags"));
        return parent::createJsonResponse("Event was created", false, 200, ["event" => $appointment]);
    }

    /**
     * @OA\Get (
     *     tags={"v1","events"},
     *     path="/api/v1/events/{eventId}/details",
     *     description="detail information about an event",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="eventId",
     *       required=true,
     *       in="path",
     *       description="id of the event",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Details about an event")
     * )
     */
    public function details($eventId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        $appointment = Appointment::find($eventId);
        $occurrenceDate = $request->input("occurrenceDate");
        if($appointment == null)
            return $this->createJsonResponse("Wrong event id supplied", true, 400);

        $meeting = ModelMeeting::where("model_type", "=", "event")->where("model_id", "=", $appointment->id)->where("type", "=", "bigbluebutton")->first();
        if($meeting == null) {
            $meeting = new ModelMeeting();
            $meeting->type = "bigbluebutton";
            $meeting->model_type = "event";
            $meeting->model_id = $appointment->id;
            $meeting->name = $appointment->name();
            try {
                $meetingController = new MeetingController();
                $meetingController->createMeeting($meeting);
            } catch (\Exception $exception)
            {
                //
            }
        }
        // load Details
        $appointment->append('attendees');
        $appointment->append('organisators');
        $appointment->load('sections');
        $appointment->load('rooms');
        $appointment->hasRepetition = !($appointment->recurrenceType == "none" || $appointment->recurrenceType == null);;
        $appointment->repetitionTurnus = $appointment->recurrenceTurnus;
        $appointment->repetitionUntil = $appointment->recurrenceUntil;

        $orga = $appointment->getOrganisatorsAttribute();
        $appointment->editable = false;
        if($orga != null && in_array($cloud_user->id,$orga->pluck("id")->toArray())) {
            $appointment->editable = true;
        }
        $singleEvent = null;
        if($appointment->hasRepetition)
        {
           $singleEvent = $appointment->getSingleEventAtDate($occurrenceDate, true);
           if($singleEvent->id == null) {
                $singleEvent->id = -1;
           } else {
                $singleEvent->append('attendees');
                $singleEvent->append('organisators');
                $singleEvent->load('sections');
                $singleEvent->load('rooms');
           }
        }
        return parent::createJsonResponse("ok", false, 200, ["event" => $appointment, "singleEvent" => $singleEvent]);
    }

    /**
     * @OA\Post (
     *     tags={"v1","events"},
     *     path="/api/v1/events/{eventId}",
     *     description="detail information about an event",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="eventId",
     *       required=true,
     *       in="path",
     *       description="id of the event",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="start",
     *       required=false,
     *       in="query",
     *       description="start timestamp",
     *         @OA\Schema(
     *           type="date"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="end",
     *       required=false,
     *       in="query",
     *       description="end timestamp",
     *         @OA\Schema(
     *           type="date"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="title",
     *       required=false,
     *       in="query",
     *       description="title of the event",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="location",
     *       required=false,
     *       in="query",
     *       description="location of the event",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="description",
     *       required=false,
     *       in="query",
     *       description="description of the event",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="color",
     *       required=false,
     *       in="query",
     *       description="color of the event",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="protocol",
     *       required=false,
     *       in="query",
     *       description="protocol of the event",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="attendees",
     *       required=true,
     *       in="query",
     *       description="array with cloud ids that are attendees of the event",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="organisators",
     *       required=true,
     *       in="query",
     *       description="array with cloud ids that are organisators of the event, the current user will automatically the organisator and it is not required to add them to this array",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="sections",
     *       required=true,
     *       in="query",
     *       description="array with sections ids that are attendes of the event",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Update the attributes of an event")
     * )
     */
    public function updateEvent($eventId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        if($request->input("type") == "single-appointment" && $request->input("action") == "move")
        {
            $appointment = SingleAppointment::find($eventId);
            $appointment->startDate = date("Y-m-d H:i", strtotime($request->input("start")));
            $appointment->endDate = date("Y-m-d H:i", strtotime($request->input("end")));
            $appointment->save();
            return parent::createJsonResponse("Event was updated", false, 200, ["event" => $appointment]);
        }

        $appointment = Appointment::find($eventId);
        if($appointment == null)
            return $this->createJsonResponse("Wrong event id supplied", true, 400);

        $oldStart = Carbon::parse($appointment->startDate);
        $startDiff = $oldStart->diff(Carbon::parse($request->input("start")));
        $oldEnd = Carbon::parse($appointment->endDate);
        $endDiff = $oldEnd->diff(Carbon::parse($request->input("end")));
        $singleAppoints = SingleAppointment::where("appointment_id","=",$appointment->id)->get();
        foreach ($singleAppoints as $singleAppoint)
        {
            if($singleAppoint->exception_type == "remove") {
            $singleAppoint->startDate = Carbon::parse($singleAppoint->startDate)->add($startDiff);
            $singleAppoint->endDate = Carbon::parse($singleAppoint->endDate)->add($endDiff);
            }
            $singleAppoint->occurrenceDate = Carbon::parse($singleAppoint->occurrenceDate)->add($startDiff);
            $singleAppoint->save();
        }
        if($request->input("action") == "move")
        {
            $appointment->startDate = date("Y-m-d H:i", strtotime($request->input("start")));
            $appointment->endDate = date("Y-m-d H:i", strtotime($request->input("end")));
        } else {

            $appointment->startDate = date("Y-m-d H:i", strtotime($request->input("start")));
            $appointment->endDate = date("Y-m-d H:i", strtotime($request->input("end")));
            $appointment->title = $request->input("title");
            $appointment->location = $request->input("location");
            $appointment->description = $request->input("description");
            $appointment->color = $request->input("color", "#3490dc");
            $appointment->protocol = $request->input("protocol");
            $appointment->remember_minutes = $request->input("remember_minutes", -1);
            $appointment->display = $request->input("display","auto");

            $appointment->rooms()->sync($request->input("rooms"));

            $teilnehmers = $request->input("attendees",[]);
            $orga = $request->input("organisators",[]);


            if (is_array($teilnehmers))
                // löschen von teilnehmern, die nicht mehr eingeladen sind
                \Illuminate\Support\Facades\DB::table('appointment_cloud_i_d')->where([
                    'appointment_id' => $appointment->id,
                ])->whereNotIn('cloudid', $teilnehmers)->where('level','=',0)->delete();
            // HInzufügen
            foreach ($teilnehmers as $teilnehmer) {
                if (!in_array($teilnehmer, $orga)) {
                    $appointment->addTeilnehmerById($teilnehmer, 0);
                }
            }

            if (is_array($orga))
                // löschen von teilnehmern, die nicht mehr eingeladen sind
                \Illuminate\Support\Facades\DB::table('appointment_cloud_i_d')->where([
                    'appointment_id' => $appointment->id,
                ])->whereNotIn('cloudid', $orga)->where('level','=',100)->delete();
            // HInzufügen
            foreach ($orga as $teilnehmer) {
                $appointment->addTeilnehmerById($teilnehmer, 0,100);
            }

            $gruppes = $request->input("sections");
            if (is_array($gruppes))
                \Illuminate\Support\Facades\DB::table('appointment_section')->where([
                    'appointment_id' => $appointment->id,
                ])->whereNotIn('section_id', $gruppes)->delete();

            foreach ($gruppes as $gruppe) {
                $appointment->addSectionById($gruppe);
            }

            if($request->has("hasRepetition") && $request->input("hasRepetition") == true)
            {
                $appointment->recurrenceUntil = Carbon::createFromTimestamp($request->input("repetitionUntil"));
                $appointment->recurrenceType = $request->input("repetitionRhythm");
                $appointment->recurrenceTurnus = $request->input("repetitionTurnus");
            } else {
                $appointment->recurrenceUntil = null;
                $appointment->recurrenceType = "none";
                $appointment->recurrenceTurnus = 1;
            }
            $appointment->save();
        }
        // Notifiy update
        foreach ($appointment->attendees as $attendee) {
            if($cloud_user->id != $attendee->id)
                FeedObserver::addUserAcitivty($attendee->id, $appointment->creator, "App\CloudID", Appointment::$FEED_UPDATED, $appointment->id, $appointment);
        }
        foreach ($appointment->organisators as $attendee) {
            if($cloud_user->id != $attendee->id)
                FeedObserver::addUserAcitivty($attendee->id, $appointment->creator, "App\CloudID", Appointment::$FEED_UPDATED, $appointment->id, $appointment);
        }

        $appointment->save();
        return parent::createJsonResponse("Event was updated", false, 200, ["event" => $appointment]);
    }

    /**
     * @OA\Post (
     *     tags={"v1","events"},
     *     path="/api/v1/events/{eventId}/delete",
     *     description="deletes an event",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="eventId",
     *       required=true,
     *       in="path",
     *       description="id of the event",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="")
     * )
     */
    public function deleteEvent($eventId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        $appointment = Appointment::find($eventId);
        if($appointment == null)
            return $this->createJsonResponse("Wrong event id supplied", true, 400);


        $appointment->delete();

        return parent::createJsonResponse("Event was deleted", false, 200);
    }

    /**
     * @OA\Post  (
     *     tags={"v1","events"},
     *     path="/api/v1/events/{eventId}/status",
     *     description="update the status of the current user according of an event; status = 0 -> vielleicht; status = 1 -> annehmen; status = 2 ablehnen",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="eventId",
     *       required=true,
     *       in="path",
     *       description="id of the event",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="status",
     *       required=true,
     *       in="query",
     *       description="new status",
     *         @OA\Schema(
     *           type="integer"
     *         )
     *     ),
     *     @OA\Response(response="200", description="the event")
     * )
     */
    public function updateStatusEvent($eventId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        $appointment = Appointment::find($eventId);
        if($appointment == null)
            return $this->createJsonResponse("Wrong event id supplied", true, 400);
        if(!$request->has("status"))
            return parent::createJsonResponse("Status is invalid", true, 400);

        $appointment->setStatus($cloud_user->id, $request->input("status"));

        return parent::createJsonResponse("Event was updated", false, 200, $appointment);
    }

    /**
     * @OA\Get (
     *     tags={"v1","task", "events"},
     *     path="/api/v1/events/invites",
     *     description="Invites of the current users",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Response(response="200", description="")
     * )
     */
    public function invites(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);

        $start = Carbon::now()->subYear();
        $end = Carbon::now()->addYears(3);
        $events = self::loadEventsForRange($start, $end, $cloud_user,[],true,  null, 0);

        return $this->createJsonResponse("ok", false,200, [ "events" => $events]);
    }

    /**
     * @OA\Get (
     *     tags={"v1","task", "events"},
     *     path="/api/v1/feed/events",
     *     description="Initial Events for the feed view",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Response(response="200", description="")
     * )
     */
    public function eventFeed(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);

        $start = Carbon::now();
        $end = Carbon::now()->addWeek();

        $groups = GroupController::loadGroups($cloud_user);
        $sectionIds = [];
        foreach ($groups as $group) {
            foreach ($group->sections as $section)
            {
                if(in_array( PermissionConstants::EDUCA_SECTION_CALENDAR_OPEN, $section->permissions) && in_array( PermissionConstants::EDUCA_SECTION_VIEW, $section->permissions))
                {
                    $sectionIds[] = $section->id;
                }
            }
        }
        $tasks = self::loadEventsForRange($start, $end, $cloud_user,$sectionIds,true, 5);
        $events = [];
        foreach ($tasks as $appoint)
        {
            $appoint->load("rooms");
            $appoint->startDate = $appoint->getStartDate()->format(\DateTime::ISO8601);
            $appoint->endDate = $appoint->getEndDate()->format(\DateTime::ISO8601);
            $events[] = $appoint;
        }
        return $this->createJsonResponse("ok", false,200, [ "events" => $events]);
    }

    /**
     * @OA\Post (
     *     tags={"v1","events"},
     *     path="/api/v1/events/checkUsers",
     *     description="check user calendars for collisions with time range",
     *     @OA\Parameter(
     *       name="token",
     *       required=true,
     *       in="query",
     *       description="token of the user",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="start",
     *       required=false,
     *       in="query",
     *       description="start timestamp",
     *         @OA\Schema(
     *           type="date"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="end",
     *       required=false,
     *       in="query",
     *       description="end timestamp",
     *         @OA\Schema(
     *           type="date"
     *         )
     *     ),
     *     @OA\Parameter(
     *       name="cloud_ids",
     *       required=true,
     *       in="query",
     *       description="array with cloud ids that are to be checked",
     *         @OA\Schema(
     *           type="string"
     *         )
     *     ),
     *     @OA\Response(response="200", description="check user calendars for collisions with time range")
     * )
     */
    public function checkUserCalendars(Request $request)
    {
        if(!$request->has("start") || !$request->has("end") || !$request->has("cloud_ids"))
        {
            return $this->createJsonResponse("Start, end and cloud ids are needed.", true, 400);
        }

        $start = Carbon::createFromTimestamp($request->input("start"))->toDateTime();
        $end = Carbon::createFromTimestamp($request->input("end"))->toDateTime();
        $cloudids = $request->input("cloud_ids",[]);

        if($request->has("exclude"))
        {
            // id des Events, welches nicht beachtet werden soll
            // wichtig für Bearbeitung existierender Events
            $exclude = $request->input("exclude");
            // Alle events im Zeitraum getten
            $events = Appointment::where("id", "!=", $exclude)->where(function($outquery) use ($start, $end) {
                $outquery->where(function($query) use ($start) {
                    // Überschneidung am Anfang des Zeitraums
                    $query->where("startDate", "<=", $start)->where("endDate", ">=", $start);
                })->orWhere(function($query) use ($end) {
                    // Überschneidung am Ende des Zeitraums
                    $query->where("startDate", "<=", $end)->where("endDate", ">=", $end);
                })->orWhere(function($query) use ($start, $end) {
                    // Überdeckung des Zeitraums
                    $query->where("startDate", "<=", $start)->where("endDate", ">=", $end);
                })->orWhere(function($query) use ($start, $end) {
                    // im Zeitraum enthalten
                    $query->where("startDate", ">=", $start)->where("endDate", "<=", $end);
                });
            })->get();
        }
        else
        {
            // Alle events im Zeitraum getten
            $events = Appointment::where(function($query) use ($start) {
                // Überschneidung am Anfang des Zeitraums
                $query->where("startDate", "<=", $start)->where("endDate", ">=", $start);
            })->orWhere(function($query) use ($end) {
                // Überschneidung am Ende des Zeitraums
                $query->where("startDate", "<=", $end)->where("endDate", ">=", $end);
            })->orWhere(function($query) use ($start, $end) {
                // Überdeckung des Zeitraums
                $query->where("startDate", "<=", $start)->where("endDate", ">=", $end);
            })->orWhere(function($query) use ($start, $end) {
                // im Zeitraum enthalten
                $query->where("startDate", ">=", $start)->where("endDate", "<=", $end);
            })->get();
        }

        $events->each->append("attendees");
        $events->each->append("organisators");

        $collisions = [];
        $totalCollisions = 0;
        foreach($cloudids as $cloudid)
        {
            $userModel = CloudID::find($cloudid);
            $collisions[$cloudid] = 0;
            foreach($events as $event)
            {
                if($event->attendees->contains($userModel) || $event->organisators->contains($userModel)) {
                    $collisions[$cloudid] += 1;
                    $totalCollisions += 1;
                }
            }
        }
        return $this->createJsonResponse("ok", false,200, [ "totalCollisions" => $totalCollisions, "collisions" => $collisions]);
    }

    public function cancelSingleEvent($eventId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        $appointment = Appointment::find($eventId);
        $occurrenceDate = $request->input("occurrenceDate");
        if($appointment == null || $occurrenceDate == null)
            return $this->createJsonResponse("Wrong event id supplied or no occurrenceDate", true, 400);

        $orga = $appointment->getOrganisatorsAttribute();
        if($cloud_user == null || $orga == null || !in_array($cloud_user->id,$orga->pluck("id")->toArray()))
            return parent::createJsonResponse("Status is invalid", true, 400);

        $singleAppointment = $appointment->getSingleEventAtDate($occurrenceDate, true);
        $singleAppointment->exception_type = "remove";
        $singleAppointment->save();

        foreach ($appointment->attendees as $attendee) {
            if($cloud_user->id != $attendee->id)
                FeedObserver::addUserAcitivty($attendee->id, $singleAppointment->creator, "App\CloudID", Appointment::$FEED_SINGLE_REMOVED, $singleAppointment->id, $singleAppointment);
        }
        foreach ($appointment->organisators as $attendee) {
            if($cloud_user->id != $attendee->id)
                FeedObserver::addUserAcitivty($attendee->id, $singleAppointment->creator, "App\CloudID", Appointment::$FEED_SINGLE_REMOVED, $singleAppointment->id, $singleAppointment);
        }

        return $this->details($eventId,$request);
    }

    public function deleteSingleEvent($eventId, $singleAppointmentId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        $appointment = Appointment::find($eventId);
        $singleAppointment = SingleAppointment::find($singleAppointmentId);
        if($appointment == null || $singleAppointment == null)
            return $this->createJsonResponse("Wrong event id supplied or wrong single appoint id", true, 400);

        $singleAppointment->delete();

        return $this->details($eventId,$request);
    }

    public function moveSingleEvent($eventId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        $appointment = Appointment::find($eventId);
        $occurrenceDate = $request->input("occurrenceDate");
        if($appointment == null || $occurrenceDate == null)
            return $this->createJsonResponse("Wrong event id supplied or no occurrenceDate", true, 400);

        $orga = $appointment->getOrganisatorsAttribute();
        if($cloud_user == null || $orga == null || !in_array($cloud_user->id,$orga->pluck("id")->toArray()))
            return parent::createJsonResponse("Status is invalid", true, 400);

        $singleAppointment = $appointment->getSingleEventAtDate($occurrenceDate, true);
        $singleAppointment->exception_type = "move";
        $singleAppointment->save();

        $singleAppointment->rooms()->sync($appointment->rooms()->get());
        $singleAppointment->sections()->sync($appointment->sections()->get());
        foreach(DB::table('appointment_cloud_i_d')->where('appointment_id', '=', $appointment->id)->get() as $entry)
        {
            DB::table('single_appointment_cloud_i_d')->insert([
                "single_appointment_id" => $singleAppointment->id,
                "cloudid" => $entry->cloudid,
                "status" => $entry->status,
                "level"  => $entry->level
            ]);
        }

        foreach ($appointment->attendees as $attendee) {
            if($cloud_user->id != $attendee->id)
                FeedObserver::addUserAcitivty($attendee->id, $singleAppointment->creator, "App\CloudID", Appointment::$FEED_SINGLE_MOVE, $singleAppointment->id, $singleAppointment);
        }
        foreach ($appointment->organisators as $attendee) {
            if($cloud_user->id != $attendee->id)
                FeedObserver::addUserAcitivty($attendee->id, $singleAppointment->creator, "App\CloudID", Appointment::$FEED_SINGLE_MOVE, $singleAppointment->id, $singleAppointment);
        }


        return $this->details($eventId,$request);
    }

    public function updateSingleEvent($eventId, $singleAppointmentId, Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        $appointment = Appointment::find($eventId);
        $singleAppointment = SingleAppointment::find($singleAppointmentId);
        if($appointment == null || $singleAppointment == null)
            return $this->createJsonResponse("Wrong event id supplied or wrong single appoint id", true, 400);

            $orga = $appointment->getOrganisatorsAttribute();
            if($cloud_user == null || $orga == null || !in_array($cloud_user->id,$orga->pluck("id")->toArray()))
                return parent::createJsonResponse("Status is invalid", true, 400);

                $eventData = $request->input("singleAppointment");
                $singleAppointment->startDate = date("Y-m-d H:i", strtotime($eventData["start"]));
                $singleAppointment->endDate = date("Y-m-d H:i", strtotime($eventData["end"]));
                $singleAppointment->title = $eventData["title"];
                $singleAppointment->location = $eventData["location"];
                $singleAppointment->description = $eventData["description"];
                $singleAppointment->color = array_key_exists("color",$eventData) ? $eventData["color"] : "#3490dc";
                $singleAppointment->protocol = $eventData["protocol"];
                $singleAppointment->remember_minutes = $eventData["remember_minutes"];
                $singleAppointment->display = $eventData["display"];
                $singleAppointment->save();
                $singleAppointment->rooms()->sync($eventData["rooms"]);


            $teilnehmers = array_key_exists("attendees",$eventData) ? $eventData["attendees"] : [];
            $orga = array_key_exists("organisators",$eventData)  ?$eventData["organisators"] : [];

                if (is_array($teilnehmers))
                // löschen von teilnehmern, die nicht mehr eingeladen sind
                \Illuminate\Support\Facades\DB::table('single_appointment_cloud_i_d')->where([
                    'single_appointment_id' => $singleAppointment->id,
                ])->whereNotIn('cloudid', $teilnehmers)->where('level','=',0)->delete();
            // HInzufügen
            foreach ($teilnehmers as $teilnehmer) {
                if (!in_array($teilnehmer, $orga)) {
                    $singleAppointment->addTeilnehmerById($teilnehmer, 0);
                }
            }

            if (is_array($orga))
                // löschen von teilnehmern, die nicht mehr eingeladen sind
                \Illuminate\Support\Facades\DB::table('single_appointment_cloud_i_d')->where([
                    'single_appointment_id' => $singleAppointment->id,
                ])->whereNotIn('cloudid', $orga)->where('level','=',100)->delete();
            // HInzufügen
            foreach ($orga as $teilnehmer) {
                $singleAppointment->addTeilnehmerById($teilnehmer, 0,100);
            }

            $sections = array_key_exists("sections",$eventData)  ?$eventData["sections"] : [];
            if (is_array($sections))
                \Illuminate\Support\Facades\DB::table('single_appointment_section')->where([
                    'single_appointment_id' => $singleAppointment->id,
                ])->whereNotIn('section_id', $sections)->delete();

            foreach ($sections as $gruppe) {
                $singleAppointment->addSectionById($gruppe);
            }

            foreach ($appointment->attendees as $attendee) {
                if($cloud_user->id != $attendee->id)
                    FeedObserver::addUserAcitivty($attendee->id, $singleAppointment->creator, "App\CloudID", Appointment::$FEED_SINGLE_MOVE, $singleAppointment->id, $singleAppointment);
            }
            foreach ($appointment->organisators as $attendee) {
                if($cloud_user->id != $attendee->id)
                    FeedObserver::addUserAcitivty($attendee->id, $singleAppointment->creator, "App\CloudID", Appointment::$FEED_SINGLE_MOVE, $singleAppointment->id, $singleAppointment);
            }

        return $this->details($eventId,$request);

    }

    public function createOutlookShareToken(Request $request)
    {
        $cloud_user = parent::getUserForToken($request);
        $token = OutlookShareToken::create($cloud_user, $request->input("filters"));

        return $this->createJsonResponse("ok", false,200, ["outlookShareToken" => $token]);

    }


}

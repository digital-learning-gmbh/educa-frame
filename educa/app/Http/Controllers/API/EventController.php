<?php

namespace App\Http\Controllers\API;

use App\Appointment;
use App\Group;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EventController extends ApiController
{
    public function events(Request $request)
    {
        $start = new \DateTime($request->input("start"));
        $end = new \DateTime($request->input("end"));
        $events = [];
        $groups = Group::find($request->input("groups"));
        if($groups == null)
        {
            $groups = [];
        }
        $appoints = $this->loadEventsForRange($groups, $request->input("direct", "false") == "true");
        foreach ($appoints as $appoint)
        {
            $events[] = $this->formatEvent($appoint);
        }

        if($request->input("dozent","false") == "true")
        {
           $controler = new UnterrichtController();
           $unterricht = $controler->getUnterrichtWithoutEntwurf($start,$end,parent::getCloudUser()->getAppLogin("klassenbuch"), "teacher");
           $events = array_merge($events,$unterricht);
        }

        return response()->json($events);
    }

    public static function loadEventsForRange($groups, $directInvation = true, Carbon $selectedDay = null)
    {
        if($directInvation) {
            $ids = \Illuminate\Support\Facades\DB::table('appointment_cloud_i_d')->where([
                'cloudid' => parent::getCloudUser()->id,
            ])->where('status', '!=', '2')
                ->pluck('appointment_id');
        } else {
            $ids = [];
        }

        if($groups != null) {
            $ids2 = \Illuminate\Support\Facades\DB::table('appointment_gruppe')
                ->whereIn('gruppe_id', $groups->pluck("id"))
                ->pluck('appointment_id');
        } else {
            $ids2 = [];
        }


        $appoints = Appointment::where(function ($query) use ($ids, $ids2) {
            $query->whereIn('id', $ids)->orWhereIn('id', $ids2);
        });
        if($selectedDay != null) {
            $appoints->where('startDate', '<=', $selectedDay->clone()->endOfDay())->where('endDate', '>=', $selectedDay->clone()->startOfDay());
        }
        $appoints = $appoints->get();

        return $appoints;
    }

    private function formatEvent($events)
    {
        $frontendEvent = [];
        $frontendEvent["id"] = $events->id;
        $frontendEvent["title"] = $events->title;
        $frontendEvent["start"] = $events->startDate;
        $frontendEvent["end"] = $events->endDate;
        $frontendEvent["color"] = $events->color;
        $frontendEvent["editable"] = false;
        $orga = $events->organisators();
        if($orga != null && ($orga->id = parent::getCloudUser()->id || in_array(parent::getCloudUser(),$orga)))
            $frontendEvent["editable"] = true;
        if(date("H:i",strtotime($events->startDate)) == "00:00" && date("H:i",strtotime($events->endDate)) == "00:00")
            $frontendEvent["allDay"] = true;
        return $frontendEvent;
    }

    public function eventDetails(Request $request)
    {
        $appoints = Appointment::find($request->input("event_id"));
        return response()->json([
            "event" => $appoints,
            "tags" => $appoints->tags(),
            "orga" => $appoints->organisators()
        ]);
    }
}

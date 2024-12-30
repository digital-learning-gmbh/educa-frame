<?php

namespace App\Http\Controllers\API\V1\Groups;

use App\Http\Controllers\API\ApiController;
use App\PermissionConstants;
use App\Section;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EventController extends ApiController
{

    /**
     * @OA\Get (
     *     tags={"v1","events", "group"},
     *     path="/api/v1/groups/section/1/events",
     *     description="Events for the section of a group",
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
     *       name="sectionId",
     *       required=true,
     *       in="path",
     *       description="ids of the section",
     *         @OA\Schema(
     *           type="string"
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
    public function events($sectionId, Request $request)
    {
        $this->cloud_user = parent::getUserForToken($request);
        if(!$request->input("start") || !$request->input("end"))
            return $this->createJsonResponse("Start and/or Enddate not defined.", true, 400);

        $start = Carbon::createFromTimestamp($request->input("start"));
        $end = Carbon::createFromTimestamp($request->input("end"));

        $section = Section::find($sectionId);
        if(!$section->isAllowed($this->cloud_user,PermissionConstants::EDUCA_CALENDAR_OPEN))
        {
            return $this->createJsonResponse("No permission.", true, 400);
        }

        $appoints = \App\Http\Controllers\API\V1\EventController::loadEventsForRange($start, $end, $this->cloud_user, [$sectionId], false);

        $events = [];
        foreach ($appoints as $appoint)
        {
            $formattedEvent = \App\Http\Controllers\API\V1\EventController::formatEvent($appoint, $this->cloud_user);
            if($formattedEvent != null)
            {
                $events[] = $formattedEvent;
            }
        }

        // Response
        if($request->has('payload') && $request->input("payload"))
        {
            return response()->json($events);
        }
        return $this->createJsonResponse("ok", false,200, [ "events" => $events]);
    }
}

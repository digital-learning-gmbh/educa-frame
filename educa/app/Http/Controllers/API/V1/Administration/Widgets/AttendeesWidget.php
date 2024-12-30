<?php


namespace App\Http\Controllers\API\V1\Administration\Widgets;

use App\Klasse;
use Illuminate\Http\Request;

class AttendeesWidget extends Widget
{
    /**
     * @OA\Get (
     *     tags={"administration", "v1", "widgets"},
     *     path="/api/v1/administration/widgets/attendees",
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
     *     name="course",
     *     required=true,
     *     in="query",
     *     description="id of the course",
     *       @OA\Schema(
     *         type="string"
     *       )
     *     ),
     *     @OA\Response(response="200", description="Returns a list of attendees")
     * )
     */
    public function loadAttendee(Request $request)
    {
        $klasse= Klasse::findOrFail($request->input("course"));
        $data = [];
        $teilnehmer = $klasse->schulerAtDatum(date("Y/m/d",strtotime($request->input("start"))))->orderBy('lastname')->orderBy('firstname')->get();
        foreach($teilnehmer as $t)
        {
            $obj["firstname"] = $t->firstname;
            $obj["lastname"] = $t->lastname;
            $obj["zeitraum"] = $t->getFormatedVonBisInKlasse($klasse->id);
            $data[] = $obj;
        }

        return parent::createJsonResponse("ok", false, 200, ["data" => $data]);
    }

}

<?php


namespace App\Http\Controllers\API\V1\Administration\Widgets;
use App\Http\Controllers\API\UnterrichtController;
use App\Klasse;
use App\LessonPlan;
use App\SchuljahrEntwurf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SchoolclassCurriculaWidget extends Widget
{
    /**
     * @OA\Get (
     *     tags={"administration", "v1", "widgets", "planning"},
     *     path="/api/v1/administration/widgets/planning/schoolclass",
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
     *     description="type of the object: teacher, room",
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
     *     @OA\Response(response="200", description="Returns all schoolclasses of a room or a teacher")
     * )
     */
    public function schoolclass(Request $request)
    {
        $data = [];
        $entwurf = SchuljahrEntwurf::findOrFail($request->input("draft"));
        $start = Carbon::createFromTimestamp($request->input("start"));
        $end = Carbon::createFromTimestamp($request->input("end"));
        $type = $request->input("type");
        $id = $request->input("id");

        $defaultUE = 60;

        if ($defaultUE == 0)
            $defaultUE = 60;

        $unterrichtsplan = UnterrichtController::getUnterrichtInternalWithoutFormat($start, $end, $type, $id, $entwurf);
        $dozentUnterricht = [];

        foreach($unterrichtsplan as $unterricht) {
            if ($unterricht instanceof LessonPlan) {
                foreach ($unterricht->klassen as $klasse) {
                    if (!array_key_exists($klasse->id, $dozentUnterricht)) {
                        $dozentUnterricht[$klasse->id] = [];
                    }
                    $dozentUnterricht[$klasse->id][] = $unterricht;
                }
            }
        }


        foreach ($dozentUnterricht as $key=>$value) {
            $node = [];
            $node["key"] = $key;
            $lehrer = Klasse::find($key);
            $node["course"] = "";
            if($lehrer != null)
            {
                $node["course"] = $lehrer->displayName;
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
